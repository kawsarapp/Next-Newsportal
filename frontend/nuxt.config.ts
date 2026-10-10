import tailwindcss from '@tailwindcss/vite'

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: false },

  // SPA Mode (Single Page Application - Zero full-page reloads)
  ssr: false,

  app: {
    pageTransition: { name: 'page', mode: 'out-in' },
    layoutTransition: { name: 'layout', mode: 'out-in' },
    head: {
      title: 'দৈনিক বাংলা — সর্বশেষ নির্ভরযোগ্য সংবাদ',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      ],
      link: [
        { rel: 'preconnect', href: 'https://fonts.googleapis.com' },
        { rel: 'preconnect', href: 'https://fonts.gstatic.com', crossorigin: '' },
        { rel: 'stylesheet', href: 'https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&display=swap' },
      ],
    },
  },

  css: ['~/assets/css/main.css'],

  vite: {
    plugins: [
      tailwindcss(),
    ],
  },

  nitro: {
    routeRules: {
      '/api/v1/**': {
        proxy: 'http://newsportal-cms.ddev.site/api/v1/**',
      },
    },
  },

  runtimeConfig: {
    public: {
      apiBase: '/api/v1',
      portalKey: process.env.NUXT_PUBLIC_PORTAL_KEY || 'pk_live_3hGHMgZc4RhMJ22FHTuqCBpU6fCOgQJd6tjv5agt',
      siteName: process.env.NUXT_PUBLIC_SITE_NAME || 'দৈনিক বাংলা',
    },
  },
})
