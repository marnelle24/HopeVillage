import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';

function getContentString(value) {
  if (value == null) return '';
  if (typeof value === 'string') return value;
  return '';
}

/**
 * Alpine.js setup for Tiptap editor bound to Livewire via entangle.
 * Includes toolbar methods for Bold, Italic, Headings, Lists, etc.
 * Use with: x-data="setupEditor($wire.entangle('body').live)"
 * Livewire v3: use .live modifier so server receives updates.
 */
window.setupEditor = function (content) {
  let editor;

  return {
    content: content,
    updatedAt: Date.now(),
    editorReady: false,

    init(element) {
      if (!element || !element.appendChild) return;
      const self = this;
      const initialContent = getContentString(this.content);
      editor = new Editor({
        element,
        extensions: [StarterKit],
        content: initialContent,
        editorProps: {
          attributes: {
            class: 'prose prose-sm sm:prose max-w-none focus:outline-none min-h-[500px] px-3 py-2 [color:#000] prose-p:text-black prose-headings:text-black prose-li:text-black',
          },
        },
        onUpdate: ({ editor: ed }) => {
          self.content = ed.getHTML();
          self.updatedAt = Date.now();
        },
        onSelectionUpdate: () => {
          self.updatedAt = Date.now();
        },
      });
      this.$watch('content', (value) => {
        if (!editor) return;
        const str = getContentString(value);
        if (str === editor.getHTML()) return;
        editor.commands.setContent(str, false);
      });
      self.editorReady = true;
      self.updatedAt = Date.now();
    },

    isLoaded() {
      return this.editorReady && !!editor;
    },

    isActive(name, attrs = {}) {
      return editor ? editor.isActive(name, attrs) : false;
    },

    toggleBold() {
      editor?.chain().focus().toggleBold().run();
    },
    toggleItalic() {
      editor?.chain().focus().toggleItalic().run();
    },
    toggleStrike() {
      editor?.chain().focus().toggleStrike().run();
    },
    toggleCode() {
      editor?.chain().focus().toggleCode().run();
    },
    setParagraph() {
      editor?.chain().focus().setParagraph().run();
    },
    toggleHeading(level) {
      editor?.chain().focus().toggleHeading({ level }).run();
    },
    toggleBulletList() {
      editor?.chain().focus().toggleBulletList().run();
    },
    toggleOrderedList() {
      editor?.chain().focus().toggleOrderedList().run();
    },
    toggleBlockquote() {
      editor?.chain().focus().toggleBlockquote().run();
    },
    setHorizontalRule() {
      editor?.chain().focus().setHorizontalRule().run();
    },
  };
};
