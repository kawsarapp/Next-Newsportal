import tailwindcss from '@tailwindcss/vite'

// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  devtools: { enabled: false },

  // SPA Mode (Single Page Application - Zero full-page reloads)
  ssr: false,

  app: {
    pageTransition: false,
    layoutTransition: false,
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
    server: {
      proxy: {
        '/api': {
          target: 'http://newsportal-cms.ddev.site',
          changeOrigin: true,
          secure: false,
        },
        '/external-news-post': {
          target: 'http://newsportal-cms.ddev.site',
          changeOrigin: true,
          secure: false,
        },
        '/get-categories': {
          target: 'http://newsportal-cms.ddev.site',
          changeOrigin: true,
          secure: false,
        },
        '/subeditor': {
          target: 'http://newsportal-cms.ddev.site',
          changeOrigin: true,
          secure: false,
        },
      },
    },
  },

  nitro: {
    routeRules: {
      '/api/**': {
        proxy: `${process.env.NUXT_PUBLIC_API_BASE?.replace('/api/v1', '') || 'http://newsportal-cms.ddev.site'}/api/**`,
      },
      '/external-news-post': {
        proxy: `${process.env.NUXT_PUBLIC_API_BASE?.replace('/api/v1', '') || 'http://newsportal-cms.ddev.site'}/external-news-post`,
      },
      '/get-categories': {
        proxy: `${process.env.NUXT_PUBLIC_API_BASE?.replace('/api/v1', '') || 'http://newsportal-cms.ddev.site'}/get-categories`,
      },
      '/subeditor/**': {
        proxy: `${process.env.NUXT_PUBLIC_API_BASE?.replace('/api/v1', '') || 'http://newsportal-cms.ddev.site'}/subeditor/**`,
      },
    },
  },

  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE || 'http://newsportal-cms.ddev.site/api/v1',
      portalKey: process.env.NUXT_PUBLIC_PORTAL_KEY || 'pk_live_3hGHMgZc4RhMJ22FHTuqCBpU6fCOgQJd6tjv5agt',
      siteName: process.env.NUXT_PUBLIC_SITE_NAME || 'দৈনিক বাংলা',
    },
  },
})
