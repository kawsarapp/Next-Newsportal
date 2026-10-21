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
      'data-orientation': {
        default: 'landscape',
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

            let orientation = element.getAttribute('data-orientation')
            const cls = element.getAttribute('class') || ''
            const style = element.getAttribute('style') || ''
            if (!orientation) {
              if (cls.includes('facebook-video-vertical') || cls.includes('aspect-[9/16]') || style.includes('9/16') || element.getAttribute('data-embed-type') === 'facebook-reel') {
                orientation = 'vertical'
              } else if (cls.includes('facebook-video-square') || cls.includes('aspect-square') || style.includes('1/1') || style.includes('1 / 1') || element.getAttribute('data-embed-type') === 'facebook-square') {
                orientation = 'square'
              } else {
                orientation = 'landscape'
              }
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
              'data-embed-type': element.getAttribute('data-embed-type') || (orientation === 'square' ? 'facebook-square' : orientation === 'vertical' ? 'facebook-reel' : 'general'),
              'data-orientation': orientation,
            }
          },
        },
      ]
    },

    renderHTML({ HTMLAttributes }) {
      return ['iframe', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes)]
    },

    addNodeView() {
      return ({ node, HTMLAttributes, getPos, editor }) => {
        const src = node.attrs.src || ''
        const isFb = src.includes('facebook') || src.includes('fb.watch') || (node.attrs['data-embed-type'] && node.attrs['data-embed-type'].includes('facebook'))
        const isYt = src.includes('youtube') || src.includes('youtu.be') || (node.attrs['data-embed-type'] && node.attrs['data-embed-type'].includes('youtube'))
        const isYtShorts = isYt && (node.attrs['data-embed-type'] === 'youtube-shorts' || node.attrs['data-orientation'] === 'vertical' || src.includes('shorts/'))

        let currentOrientation = node.attrs['data-orientation']
        if (!currentOrientation || currentOrientation === 'landscape') {
          const cls = node.attrs.class || ''
          const sty = node.attrs.style || ''
          if (cls.includes('facebook-video-vertical') || node.attrs['data-embed-type'] === 'facebook-reel' || isYtShorts || sty.includes('9/16')) {
            currentOrientation = 'vertical'
          } else if (cls.includes('facebook-video-square') || cls.includes('aspect-square') || node.attrs['data-embed-type'] === 'facebook-square' || sty.includes('1/1') || sty.includes('1 / 1')) {
            currentOrientation = 'square'
          } else {
            currentOrientation = 'landscape'
          }
        }

        const containerMaxWidth = currentOrientation === 'vertical' ? 'max-w-[380px]' : currentOrientation === 'square' ? 'max-w-[520px]' : 'max-w-[680px]'

        const dom = document.createElement('div')
        dom.className = `relative my-6 group tiptap-iframe-container select-none ${containerMaxWidth} mx-auto`
        dom.setAttribute('data-tiptap-iframe', 'true')

        const iframe = document.createElement('iframe')
        const mergedAttrs = mergeAttributes(this.options.HTMLAttributes, HTMLAttributes)
        Object.entries(mergedAttrs).forEach(([key, val]) => {
          if (val !== null && val !== undefined) {
            iframe.setAttribute(key, val)
          }
        })
        iframe.style.pointerEvents = 'none'

        const overlay = document.createElement('div')
        overlay.className = 'absolute inset-0 z-10 cursor-pointer rounded-xl border-2 border-transparent transition-all hover:bg-black/5 hover:border-red-300'
        overlay.title = 'ভিডিওটি সিলেক্ট করতে ক্লিক করুন অথবা Backspace/Delete চেপে মুছুন'

        const controlBar = document.createElement('div')
        controlBar.className = 'absolute top-3 right-3 z-20 flex items-center gap-1.5 opacity-90 group-hover:opacity-100 transition-opacity flex-wrap justify-end'

        // Facebook ratio switcher buttons directly in the editor!
        if (isFb) {
          const ratioGroup = document.createElement('div')
          ratioGroup.className = 'flex items-center bg-white/95 backdrop-blur-xs rounded-md shadow border border-gray-200 overflow-hidden p-0.5 gap-0.5 text-xs'

          const ratios = [
            { key: 'landscape', label: '16:9', icon: '💻', title: 'ল্যান্ডস্কেপ (16:9) - সাধারণ অনুভূমিক ভিডিও' },
            { key: 'square', label: '1:1', icon: '⏹️', title: 'স্কয়ার (1:1) - চারকোনা ফেসবুক ভিডিও (কাটা পড়বে না)' },
            { key: 'vertical', label: '9:16', icon: '📱', title: 'রিলস / খাড়া (9:16)' },
          ]

          ratios.forEach((r) => {
            const btn = document.createElement('button')
            btn.type = 'button'
            btn.title = r.title
            const isActive = currentOrientation === r.key
            btn.className = `px-2 py-0.5 rounded text-[11px] font-semibold flex items-center gap-1 transition-all cursor-pointer ${
              isActive ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-700 hover:bg-gray-100'
            }`
            btn.innerHTML = `<span>${r.icon}</span><span>${r.label}</span>`

            btn.addEventListener('mousedown', (e) => {
              e.preventDefault()
              e.stopPropagation()
            })

            btn.addEventListener('click', (e) => {
              e.preventDefault()
              e.stopPropagation()
              if (typeof getPos === 'function') {
                const pos = getPos()
                let newClass = 'w-full rounded-xl my-4 shadow-sm '
                let newStyle = 'border:none;overflow:hidden;width:100%;height:auto;'
                let widthParam = 680

                if (r.key === 'vertical') {
                  newClass += 'facebook-video facebook-video-vertical aspect-[9/16]'
                  newStyle += 'aspect-ratio: 9/16; max-width: 380px; background-color: #000;'
                  widthParam = 360
                } else if (r.key === 'square') {
                  newClass += 'facebook-video facebook-video-square aspect-square'
                  newStyle += 'aspect-ratio: 1/1; max-width: 520px;'
                  widthParam = 500
                } else {
                  newClass += 'facebook-video facebook-video-landscape aspect-video'
                  newStyle += 'aspect-ratio: 16/9; max-width: 680px;'
                  widthParam = 680
                }

                let newSrc = node.attrs.src || ''
                if (newSrc.includes('plugins/video.php')) {
                  newSrc = newSrc.replace(/([?&])width=\d+/, `$1width=${widthParam}`)
                }

                // Immediately update DOM for zero lag
                currentOrientation = r.key
                dom.className = `relative my-6 group tiptap-iframe-container select-none ${r.key === 'vertical' ? 'max-w-[380px]' : r.key === 'square' ? 'max-w-[520px]' : 'max-w-[680px]'} mx-auto`
                iframe.className = newClass
                iframe.style.cssText = newStyle
                iframe.setAttribute('data-orientation', r.key)
                iframe.setAttribute('data-embed-type', r.key === 'vertical' ? 'facebook-reel' : r.key === 'square' ? 'facebook-square' : 'facebook-video')
                if (newSrc && iframe.getAttribute('src') !== newSrc) {
                  iframe.setAttribute('src', newSrc)
                }

                // Update ratio buttons active state immediately
                ratioGroup.querySelectorAll('button').forEach((b, idx) => {
                  const isSelected = ratios[idx].key === r.key
                  b.className = `px-2 py-0.5 rounded text-[11px] font-semibold flex items-center gap-1 transition-all cursor-pointer ${
                    isSelected ? 'bg-blue-600 text-white shadow-xs' : 'text-gray-700 hover:bg-gray-100'
                  }`
                })

                // Update badge text
                if (r.key === 'vertical') {
                  badge.textContent = '📱 ফেসবুক রিল (9:16)'
                } else if (r.key === 'square') {
                  badge.textContent = '⏹️ ফেসবুক স্কয়ার (1:1)'
                } else {
                  badge.textContent = '📘 ফেসবুক (16:9)'
                }

                const tr = editor.state.tr.setNodeMarkup(pos, undefined, {
                  ...node.attrs,
                  src: newSrc,
                  'data-orientation': r.key,
                  'data-embed-type': r.key === 'vertical' ? 'facebook-reel' : r.key === 'square' ? 'facebook-square' : 'facebook-video',
                  class: newClass,
                  style: newStyle,
                })
                editor.view.dispatch(tr)
              }
            })

            ratioGroup.appendChild(btn)
          })

          controlBar.appendChild(ratioGroup)
        }

      const badge = document.createElement('span')
      badge.className = 'px-2.5 py-1 text-[11px] font-semibold text-gray-700 bg-white/95 backdrop-blur-xs rounded-md shadow border border-gray-200 pointer-events-none'
      if (isFb) {
        if (currentOrientation === 'vertical') {
          badge.textContent = '📱 ফেসবুক রিল (9:16)'
        } else if (currentOrientation === 'square') {
          badge.textContent = '⏹️ ফেসবুক স্কয়ার (1:1)'
        } else {
          badge.textContent = '📘 ফেসবুক (16:9)'
        }
      } else if (isYtShorts) {
        badge.textContent = '📱 ইউটিউব শর্টস (9:16)'
      } else if (isYt) {
        badge.textContent = '▶️ ইউটিউব (16:9)'
      } else {
        badge.textContent = '📹 ভিডিও'
      }

      const deleteBtn = document.createElement('button')
      deleteBtn.type = 'button'
      deleteBtn.className = 'flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md shadow transition-colors cursor-pointer'
      deleteBtn.innerHTML = '<span>🗑️</span><span>মুছে ফেলুন</span>'
      deleteBtn.title = 'ভিডিওটি মুছে ফেলুন (Delete)'

      const handleDelete = (e) => {
        e.preventDefault()
        e.stopPropagation()
        if (typeof getPos === 'function') {
          const pos = getPos()
          editor.chain().focus().deleteRange({ from: pos, to: pos + node.nodeSize }).run()
        }
      }

      deleteBtn.addEventListener('click', handleDelete)
      deleteBtn.addEventListener('mousedown', (e) => {
        e.preventDefault()
        e.stopPropagation()
      })

      overlay.addEventListener('click', (e) => {
        e.preventDefault()
        e.stopPropagation()
        if (typeof getPos === 'function') {
          const pos = getPos()
          editor.commands.setNodeSelection(pos)
        }
      })

      controlBar.appendChild(badge)
      controlBar.appendChild(deleteBtn)

      dom.appendChild(iframe)
      dom.appendChild(overlay)
      dom.appendChild(controlBar)

      return {
        dom,
        update: (updatedNode) => {
          if (updatedNode.type.name !== node.type.name) return false
          node = updatedNode
          const orientation = updatedNode.attrs['data-orientation'] || 'landscape'
          dom.className = `relative my-6 group tiptap-iframe-container select-none ${orientation === 'vertical' ? 'max-w-[380px]' : orientation === 'square' ? 'max-w-[520px]' : 'max-w-[680px]'} mx-auto`
          if (updatedNode.attrs.class) {
            iframe.className = updatedNode.attrs.class
          }
          if (updatedNode.attrs.style) {
            iframe.style.cssText = updatedNode.attrs.style
          }
          return true
        },
        selectNode: () => {
          dom.classList.add('ProseMirror-selectednode')
          overlay.classList.remove('border-transparent', 'hover:border-red-300')
          overlay.classList.add('border-red-500', 'bg-red-500/10', 'ring-4', 'ring-red-500/25')
        },
        deselectNode: () => {
          dom.classList.remove('ProseMirror-selectednode')
          overlay.classList.remove('border-red-500', 'bg-red-500/10', 'ring-4', 'ring-red-500/25')
          overlay.classList.add('border-transparent', 'hover:border-red-300')
        },
        stopEvent: (event) => {
          return controlBar.contains(event.target)
        },
        ignoreMutation: () => true,
        destroy: () => {
          deleteBtn.removeEventListener('click', handleDelete)
        },
      }
    }
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
