import { Editor, Extension, InputRule } from '@tiptap/core';
import { TextStyle } from '@tiptap/extension-text-style'
import { Image } from '@tiptap/extension-image'
import { Link } from '@tiptap/extension-link'
import StarterKit from '@tiptap/starter-kit';
import Heading from '@tiptap/extension-heading'
import Bold from '@tiptap/extension-bold'
import Italic from '@tiptap/extension-italic'
import Paragraph from '@tiptap/extension-paragraph'
import { Markdown } from '@tiptap/markdown';
import { Placeholder } from "@tiptap/extension-placeholder";
import Underline from '@tiptap/extension-underline'
import FontFamily from '@tiptap/extension-font-family'
import TextAlign from '@tiptap/extension-text-align'
import MarkdownIt from 'markdown-it';
let snapshots = [];
const CustomHeading = Heading.extend({
  addAttributes() {
    return {
      style: {
        default: null,
        parseHTML: element => element.getAttribute('style'),
        renderHTML: attributes => attributes.style ? { style: attributes.style } : {},
      },
    }
  },
})

const CustomParagraph = Paragraph.extend({
  addAttributes() {
    return {
      style: {
        default: null,
        parseHTML: element => element.getAttribute('style'),
        renderHTML: attributes => attributes.style ? { style: attributes.style } : {},
      },
    }
  },
})

const CustomBold = Bold.extend({
  addAttributes() {
    return {
      style: {
        default: null,
        parseHTML: element => element.getAttribute('style'),
        renderHTML: attributes => attributes.style ? { style: attributes.style } : {},
      },
    }
  },
})

const CustomItalic = Italic.extend({
  addAttributes() {
    return {
      style: {
        default: null,
        parseHTML: element => element.getAttribute('style'),
        renderHTML: attributes => attributes.style ? { style: attributes.style } : {},
      },
    }
  },
})

const HtmlInputRules = Extension.create({
  name: 'htmlInputRules',
  addInputRules() {
    return [
      new InputRule({
        find: /<b(.*?)>(.*?)<\/b>$/g,
        handler: ({ state, range, match }) => {
          const { tr } = state;
          const styleMatch = match[1].match(/style=["'](.*?)["']/);
          const style = styleMatch ? styleMatch[1] : null;
          const content = match[2];
          const mark = this.editor.schema.marks.bold.create({ style });
          const textNode = content ? this.editor.schema.text(content, [mark]) : null;
          tr.replaceWith(range.from, range.to, textNode);
        },
      }),
      new InputRule({
        find: /<strong(.*?)>(.*?)<\/strong>$/g,
        handler: ({ state, range, match }) => {
          const { tr } = state;
          const styleMatch = match[1].match(/style=["'](.*?)["']/);
          const style = styleMatch ? styleMatch[1] : null;
          const content = match[2];
          const mark = this.editor.schema.marks.bold.create({ style });
          const textNode = content ? this.editor.schema.text(content, [mark]) : null;
          tr.replaceWith(range.from, range.to, textNode);
        },
      }),
      new InputRule({
        find: /<i(.*?)>(.*?)<\/i>$/g,
        handler: ({ state, range, match }) => {
          const { tr } = state;
          const styleMatch = match[1].match(/style=["'](.*?)["']/);
          const style = styleMatch ? styleMatch[1] : null;
          const content = match[2];
          const mark = this.editor.schema.marks.italic.create({ style });
          const textNode = content ? this.editor.schema.text(content, [mark]) : null;
          tr.replaceWith(range.from, range.to, textNode);
        },
      }),
      new InputRule({
        find: /<em(.*?)>(.*?)<\/em>$/g,
        handler: ({ state, range, match }) => {
          const { tr } = state;
          const styleMatch = match[1].match(/style=["'](.*?)["']/);
          const style = styleMatch ? styleMatch[1] : null;
          const content = match[2];
          const mark = this.editor.schema.marks.italic.create({ style });
          const textNode = content ? this.editor.schema.text(content, [mark]) : null;
          tr.replaceWith(range.from, range.to, textNode);
        },
      }),
      new InputRule({
        find: /<h1(.*?)>(.*?)<\/h1>$/g,
        handler: ({ state, range, match }) => {
          const { tr } = state;
          const styleMatch = match[1].match(/style=["'](.*?)["']/);
          const style = styleMatch ? styleMatch[1] : null;
          const content = match[2];
          const textNode = content ? this.editor.schema.text(content) : null;
          tr.replaceWith(range.from, range.to, this.editor.schema.nodes.heading.create({ level: 1, style }, textNode));
        },
      }),
      new InputRule({
        find: /<h2(.*?)>(.*?)<\/h2>$/g,
        handler: ({ state, range, match }) => {
          const { tr } = state;
          const styleMatch = match[1].match(/style=["'](.*?)["']/);
          const style = styleMatch ? styleMatch[1] : null;
          const content = match[2];
          const textNode = content ? this.editor.schema.text(content) : null;
          tr.replaceWith(range.from, range.to, this.editor.schema.nodes.heading.create({ level: 2, style }, textNode));
        },
      }),
      new InputRule({
        find: /<h3(.*?)>(.*?)<\/h3>$/g,
        handler: ({ state, range, match }) => {
          const { tr } = state;
          const styleMatch = match[1].match(/style=["'](.*?)["']/);
          const style = styleMatch ? styleMatch[1] : null;
          const content = match[2];
          const textNode = content ? this.editor.schema.text(content) : null;
          tr.replaceWith(range.from, range.to, this.editor.schema.nodes.heading.create({ level: 3, style }, textNode));
        },
      }),
      new InputRule({
        find: /<p(.*?)>(.*?)<\/p>$/g,
        handler: ({ state, range, match }) => {
          const { tr } = state;
          const styleMatch = match[1].match(/style=["'](.*?)["']/);
          const style = styleMatch ? styleMatch[1] : null;
          const content = match[2];
          const textNode = content ? this.editor.schema.text(content) : null;
          tr.replaceWith(range.from, range.to, this.editor.schema.nodes.paragraph.create({ style }, textNode));
        },
      }),
    ]
  },
})

const editorElement = document.querySelector("#editor")
if (editorElement) {
  const editor = new Editor({
    element: editorElement,
    extensions: [
      StarterKit.configure({
        heading: false,
        bold: false,
        italic: false,
        paragraph: false,
        history: true,
      }),
      CustomHeading,
      CustomParagraph,
      CustomBold,
      CustomItalic,
      Link.configure({
        openOnClick: false,
        autolink: true,
        linkOnPaste: true,
      }),
      Image,
      Markdown,
      TextStyle,
      HtmlInputRules,
      Placeholder.configure({
        emptyNodeClass: 'before:text-2xl',
        placeholder: 'Tell your story ...'
      }),
      Underline,
      FontFamily,
      TextAlign.configure({
        types: ['heading', 'paragraph'],
      }),
    ],
    editorProps: {
      handlePaste(event) {
        const text = event.clipboardData.getData('text/plain');
        const html = event.clipboardData.getData('text/html');

        if (text && !html && (text.includes('```') || text.includes('**') || text.includes('#'))) {
          const mdInstance = new MarkdownIt();
          const parsedHtml = mdInstance.render(text);
          editor.commands.insertContent(parsedHtml);
          return true;
        }
        return false;
      },
    },
    content: '',
  })

  const commitBtn = document.querySelector('#commit_btn');
  const commitMsgInput = document.querySelector('#commit_message_input');

  function takeSnapshot(message) {
    snapshots.push({
      message,
      time: new Date(),
      content: editor.getHTML()
    });
    console.log(`Snapshot taken: "${message}"`);
    renderCommitHistory();
  }

  commitBtn?.addEventListener('click', () => {
    const commitMessage = commitMsgInput?.value || 'Untitled snapshot';
    takeSnapshot(commitMessage);
    if (commitMsgInput) commitMsgInput.value = '';
  });

  const revertBtn = document.querySelector('#revert_btn');
  revertBtn?.addEventListener('click', () => {
    const lastSnapshot = snapshots[snapshots.length - 1];
    if (!lastSnapshot) return alert('No snapshots to revert');
    editor.commands.setContent(lastSnapshot.content);
    renderCommitHistory();
  });

  // Revert button logic is now also handled per commit in renderCommitHistory
  function renderCommitHistory() {
    if (!editor || !editor.state) return;
    const historyContainer = document.querySelector('#history_container');
    if (!historyContainer) return;
    historyContainer.innerHTML = '';

    console.log('Rendering history. Snapshots:', snapshots.length);

    if (snapshots.length === 0) {
      historyContainer.innerHTML = '<div style="color: #64748b; font-size: 0.85rem; padding: 12px 0; text-align: center;">No snapshots yet.</div>';
      return;
    }

    [...snapshots].reverse().forEach((snapshot, index) => {
      const commitElement = document.createElement('div');
      commitElement.className = 'snapshot-item';
      commitElement.style.display = 'flex';
      commitElement.style.justifyContent = 'space-between';
      commitElement.style.alignItems = 'center';

      const infoSpan = document.createElement('div');
      infoSpan.style.overflow = 'hidden';
      infoSpan.innerHTML = `
        <div class="snapshot-name">${snapshot.message}</div>
        <div class="snapshot-time">${snapshot.time.toLocaleTimeString()}</div>
      `;

      const revertBtn = document.createElement('button');
      revertBtn.type = 'button';
      revertBtn.className = 'sidebar-action-btn';
      revertBtn.style.padding = '4px 8px';
      revertBtn.style.fontSize = '12px';
      revertBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>';
      revertBtn.title = 'Restore this version';
      revertBtn.addEventListener('click', () => {
        if (confirm(`Restore version: "${snapshot.message}"?`)) {
          editor.commands.setContent(snapshot.content);
        }
      });

      commitElement.appendChild(infoSpan);
      commitElement.appendChild(revertBtn);
      historyContainer.appendChild(commitElement);
    });
  }
  const importBtn = document.querySelector('#import_md_btn');
  const mdFileInput = document.querySelector('#md_file_input');
  const undoBtn = document.querySelector('#undo_btn');
  const redoBtn = document.querySelector('#redo_btn');
  const boldBtn = document.querySelector('#bold_btn');
  const italicBtn = document.querySelector('#italic_btn');
  const underlineBtn = document.querySelector('#underline_btn');
  const bulletBtn = document.querySelector('#bullet_btn');
  const orderedBtn = document.querySelector('#ordered_btn');
  const headingSelect = document.querySelector('#heading_select');
  const fontSelect = document.querySelector('#font_select');
  const linkBtn = document.querySelector('#link_btn');
  const imageBtn = document.querySelector('#image_btn');
  const imageInput = document.querySelector('#image_upload')
  const alignBtn = document.querySelector('#align_btn');

  importBtn?.addEventListener('click', () => {
    mdFileInput?.click()
  })
  mdFileInput?.addEventListener('change', () => {
    const file = mdFileInput.files[0]
    if (!file) return
    const reader = new FileReader()
    reader.onload = (event) => {
      const markdown = event.target.result
      const md = new MarkdownIt();
      const html = md.render(markdown);
      editor.commands.setContent(html);
    }
    reader.readAsText(file)
    mdFileInput.value = ''
  })


  imageBtn?.addEventListener('click', () => {
    imageInput?.click()
  })
  imageInput?.addEventListener('change', async () => {
    const file = imageInput.files[0]
    if (!file) return
    const formData = new FormData()
    formData.append('image', file)
    try {
      const response = await fetch('/upload-image', {
        method: 'POST',
        body: formData
      })
      const data = await response.json()
      if (data.url) {
        editor.chain().focus().setImage({ src: data.url }).run()
      }
    } catch (error) {
      console.error('Error uploading image:', error)
    }
    imageInput.value = ''
  })

  linkBtn?.addEventListener('click', () => {
    const previousUrl = editor.getAttributes('link').href || ''
    const url = prompt('Enter the URL', previousUrl)
    if (url === null) return

    if (url === '') {
      editor.chain().focus().unsetLink().run()
      return
    }
    editor.chain().focus().setLink({ href: url }).run()
  })




  undoBtn?.addEventListener('click', () => {
    editor.chain().focus().undo().run()
  })

  redoBtn?.addEventListener('click', () => {
    editor.chain().focus().redo().run()
  })

  boldBtn?.addEventListener('click', () => {
    editor.chain().focus().toggleBold().run()
  })
  underlineBtn?.addEventListener('click', () => {
    editor.chain().focus().toggleUnderline().run()
  })

  italicBtn?.addEventListener('click', () => {
    editor.chain().focus().toggleItalic().run()
  })
  bulletBtn?.addEventListener('click', () => {
    editor.chain().focus().toggleBulletList().run()
  })
  orderedBtn?.addEventListener('click', () => {
    editor.chain().focus().toggleOrderedList().run()
  })
  alignBtn?.addEventListener('click', () => {
    const isLeft = editor.isActive({ textAlign: 'left' })
    const isCenter = editor.isActive({ textAlign: 'center' })
    const isRight = editor.isActive({ textAlign: 'right' })

    if (isRight) editor.chain().focus().setTextAlign('center').run()
    else if (isCenter) editor.chain().focus().setTextAlign('right').run()
    else editor.chain().focus().setTextAlign('left').run()
  })

  headingSelect?.addEventListener('change', (e) => {
    const value = e.target.value
    if (value === '') {
      editor.chain().focus().setParagraph().run()
    } else {
      editor.chain().focus().toggleHeading({ level: Number(value) }).run()
    }
  })
  fontSelect?.addEventListener('change', (e) => {
    const value = e.target.value
    if (value === '') {
      editor.chain().focus().unsetFontFamily().run()
    } else {
      editor.chain().focus().setFontFamily(value).run()
    }
  })
  function bindHistoryButton(button, command) {
    if (!button) return
    const update = () => {
      button.disabled = !editor.can().chain().focus()[command]().run()
    }
    editor.on('selectionUpdate', update)
    editor.on('transaction', update)
    update()
  }
  bindHistoryButton(undoBtn, 'undo')
  bindHistoryButton(redoBtn, 'redo')

  function bindToolbarButton(button, config) {
    if (!button) return
    const update = () => {
      button.classList.toggle('active', config.isActive())
      button.disabled = !config.canRun()
    }
    editor.on('selectionUpdate', update)
    editor.on('transaction', update)
    update()
  }
  bindToolbarButton(boldBtn, {
    isActive: () => editor.isActive('bold'),
    canRun: () => editor.can().chain().focus().toggleBold().run(),
  })

  bindToolbarButton(italicBtn, {
    isActive: () => editor.isActive('italic'),
    canRun: () => editor.can().chain().focus().toggleItalic().run(),
  })
  bindToolbarButton(underlineBtn, {
    isActive: () => editor.isActive('underline'),
    canRun: () => editor.can().chain().focus().toggleUnderline().run(),
  })


  bindToolbarButton(bulletBtn, {
    isActive: () => editor.isActive('bulletList'),
    canRun: () => editor.can().chain().focus().toggleBulletList().run(),
  })


  bindToolbarButton(orderedBtn, {
    isActive: () => editor.isActive('orderedList'),
    canRun: () => editor.can().chain().focus().toggleOrderedList().run(),
  })
  bindToolbarButton(alignBtn, {
    isActive: () => editor.isActive({ textAlign: 'center' }) || editor.isActive({ textAlign: 'right' }),
    canRun: () => editor.can().chain().focus().setTextAlign('center').run(),
  })
  bindToolbarButton(linkBtn, {
    isActive: () => editor.isActive('link'),
    canRun: () => editor.can().chain().focus().setLink({
      href: 'https://example.com'
    }).run(),
  })
  function updateHeadingSelect() {
    if (!headingSelect) return
    if (editor.isActive('heading', { level: 1 })) {
      headingSelect.value = '1'
    } else if (editor.isActive('heading', { level: 2 })) {
      headingSelect.value = '2'
    } else {
      headingSelect.value = ''
    }
  }

  function updateFontSelect() {
    if (!fontSelect) return
    const fontFamily = editor.getAttributes('textStyle').fontFamily || ''
    fontSelect.value = fontFamily
  }

  editor.on('selectionUpdate', () => {
    updateHeadingSelect()
    updateFontSelect()
  })
  editor.on('transaction', () => {
    updateHeadingSelect()
    updateFontSelect()
  })
  updateHeadingSelect()
  updateFontSelect()

  function updateToolbar() {
    boldBtn?.classList.toggle('active', editor.isActive('bold'))
    italicBtn?.classList.toggle('active', editor.isActive('italic'))
    underlineBtn?.classList.toggle('active', editor.isActive('underline'))
  }
  editor.on('selectionUpdate', updateToolbar)
  editor.on('transaction', updateToolbar)


  function bindActiveState(button, checkFn) {
    if (!button || typeof checkFn !== 'function') return
    const update = () => {
      button.classList.toggle('active', checkFn())
    }
    editor.on('selectionUpdate', update)
    editor.on('transaction', update)
  };
  bindActiveState(boldBtn, () => editor.isActive('bold'));
  bindActiveState(italicBtn, () => editor.isActive('italic'));
  bindActiveState(underlineBtn, () => editor.isActive('underline'));
  const form = editorElement.closest('form')
  const hiddenInput = document.querySelector('#content_input')
  console.log('Editor JS loaded. Hidden input found:', !!hiddenInput);
  if (hiddenInput && hiddenInput.value) {
    editor.commands.setContent(hiddenInput.value);
  }
  // Take an initial snapshot
  setTimeout(() => {
    takeSnapshot('Initial version');
  }, 100);
  form?.addEventListener('submit', () => {
    const html = editor.getHTML();
    console.log('Form submitting. HTML length:', html.length);
    if (hiddenInput) {
      hiddenInput.value = html;
    } else {
      console.error('Hidden input #content_input not found on submit!');
    }
  })
}
