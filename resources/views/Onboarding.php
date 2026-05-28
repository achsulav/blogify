<style>
.onboarding-wrapper {
    position: fixed;
    top: 0; left: 0; width: 100vw; height: 100vh;
    background: #fff;
    z-index: 9999;
    overflow-y: auto;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}
.onboarding-container {
    max-width: 800px;
    margin: 60px auto;
    padding: 20px;
    text-align: center;
}
.onboarding-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 10px;
    color: #111827;
}
.onboarding-subtitle {
    font-size: 1.1rem;
    color: #6b7280;
    margin-bottom: 40px;
}
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 40px;
}
.category-card {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    user-select: none;
}
.category-card:hover {
    border-color: #d1d5db;
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
.category-card.selected {
    border-color: #4f46e5;
    background-color: #eef2ff;
    color: #4f46e5;
}
.category-icon {
    font-size: 2rem;
    margin-bottom: 10px;
}
.category-name {
    font-weight: 600;
    font-size: 0.95rem;
}
.continue-btn {
    background: #4f46e5;
    color: #fff;
    border: none;
    padding: 15px 40px;
    font-size: 1.1rem;
    font-weight: bold;
    border-radius: 30px;
    cursor: pointer;
    transition: background 0.2s;
    box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
}
.continue-btn:hover {
    background: #4338ca;
}
.continue-btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    box-shadow: none;
}
.search-input {
    width: 100%;
    max-width: 400px;
    padding: 12px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 30px;
    font-size: 1rem;
    margin-bottom: 30px;
    outline: none;
    transition: border-color 0.2s;
}
.search-input:focus {
    border-color: #4f46e5;
}
</style>

<?php 
$isSettings = $isSettings ?? false; 
$userInterests = $userInterests ?? [];
?>
<div class="onboarding-wrapper">
    <div class="onboarding-container">
        <?php if ($isSettings): ?>
            <h1 class="onboarding-title">Update Your Interests & Role</h1>
            <p class="onboarding-subtitle">Change your role and selected categories to refresh your feed.</p>
        <?php else: ?>
            <h1 class="onboarding-title">What best describes you?</h1>
            <p class="onboarding-subtitle">Select your primary role and at least 3 categories to personalize your feed.</p>
        <?php endif; ?>
        
        <div style="margin-bottom: 30px; text-align: left; max-width: 400px; margin-left: auto; margin-right: auto;">
            <label style="font-weight: 600; margin-bottom: 8px; display: block;">Primary Role:</label>
            <select id="userRole" class="search-input" style="margin-bottom: 0;">
                <?php 
                $roles = ['Primary Audience', 'Writer', 'Researcher', 'Developer', 'Designer', 'Founder', 'Student', 'Reader', 'Marketer'];
                $currentRole = $userRole ?? 'Primary Audience';
                foreach($roles as $r): ?>
                    <option value="<?= $r ?>" <?= $currentRole === $r ? 'selected' : '' ?>><?= $r ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <input type="text" id="categorySearch" class="search-input" placeholder="Search interests..." style="margin-top: 20px;">

        <div class="categories-grid" id="categoriesGrid">
            <?php foreach ($categories as $category): 
                $isSelected = in_array($category['id'], $userInterests);
            ?>
                <div class="category-card <?= $isSelected ? 'selected' : '' ?>" data-id="<?= $category['id'] ?>" data-name="<?= strtolower($category['name']) ?>">
                    <div class="category-icon"><?= $category['icon'] ?? '📌' ?></div>
                    <div class="category-name"><?= htmlspecialchars($category['name']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <button id="continueBtn" class="continue-btn" disabled>Continue</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = document.querySelectorAll('.category-card');
    const continueBtn = document.getElementById('continueBtn');
    const searchInput = document.getElementById('categorySearch');
    let selectedIds = new Set(<?= json_encode($userInterests) ?>.map(String));

    updateBtnState(); // Init state

    cards.forEach(card => {
        card.addEventListener('click', () => {
            const id = card.getAttribute('data-id');
            if (selectedIds.has(id)) {
                selectedIds.delete(id);
                card.classList.remove('selected');
            } else {
                selectedIds.add(id);
                card.classList.add('selected');
            }
            updateBtnState();
        });
    });

    searchInput.addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            if (name.includes(term)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });

    function updateBtnState() {
        if (selectedIds.size >= 3) {
            continueBtn.removeAttribute('disabled');
            continueBtn.textContent = `Continue (${selectedIds.size} selected)`;
        } else {
            continueBtn.setAttribute('disabled', 'true');
            continueBtn.textContent = `Select ${3 - selectedIds.size} more`;
        }
    }

    continueBtn.addEventListener('click', () => {
        if (selectedIds.size < 3) return;
        
        const btnText = continueBtn.textContent;
        continueBtn.textContent = 'Saving...';
        continueBtn.disabled = true;

        const selectedRole = document.getElementById('userRole').value;

        fetch('/api/onboarding/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                role: selectedRole,
                categories: Array.from(selectedIds)
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect;
            } else {
                alert('Error: ' + data.message);
                continueBtn.textContent = btnText;
                continueBtn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Something went wrong!');
            continueBtn.textContent = btnText;
            continueBtn.disabled = false;
        });
    });
});
</script>
