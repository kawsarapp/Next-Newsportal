import { Node, mergeAttributes } from '@tiptap/core'

export const Iframe = Node.create({
  name: 'iframe',

  group: 'block',

  selectable: true,

  draggable: true,

  atom: true,

  addOptions() {
    return {
      allowFullscreen: true,
      HTMLAttributes: {},
    }
  },

  addAttributes() {
    return {
      src: {
        default: null,
      },
      frameborder: {
        default: '0',
      },
      allowfullscreen: {
        default: this.options.allowFullscreen ? 'true' : null,
      },
      allow: {
        default: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share',
      },
      width: {
        default: '100%',
      },
      height: {
        default: '450',
      },
      style: {
        default: null,
      },
      class: {
        default: 'w-full rounded-xl my-4 aspect-video shadow-sm',
      },
      scrolling: {
        default: 'no',
      },
      'data-embed-type': {
        default: 'general',
      },
    }
  },

  parseHTML() {
    return [
      {
        tag: 'iframe[src]',
        getAttrs: (element) => {
          if (!(element instanceof HTMLElement)) {
            return false
          }

          return {
            src: element.getAttribute('src'),
            width: element.getAttribute('width') || '100%',
            height: element.getAttribute('height') || '450',
            frameborder: element.getAttribute('frameborder') || '0',
            allowfullscreen: element.getAttribute('allowfullscreen') || 'true',
            allow: element.getAttribute('allow') || 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share',
            style: element.getAttribute('style') || null,
            class: element.getAttribute('class') || 'w-full rounded-xl my-4 aspect-video shadow-sm',
            scrolling: element.getAttribute('scrolling') || 'no',
            'data-embed-type': element.getAttribute('data-embed-type') || 'general',
          }
        },
      },
    ]
  },

  renderHTML({ HTMLAttributes }) {
    return ['iframe', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes)]
  },

  addCommands() {
    return {
      setIframe: (options) => ({ commands }) => {
        return commands.insertContent({
          type: this.name,
          attrs: options,
        })
      },
    }
  },
})

export default Iframe
