import dns from 'node:dns'

export default defineNitroPlugin(() => {
  // Allow local self-signed certificates in dev/local environments
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = '0'

  // Ensure newsportal-cms.ddev.site resolves to 127.0.0.1 in local environments without breaking async contracts
  const origLookup = dns.lookup
  dns.lookup = ((hostname: string, options: any, callback: any) => {
    let cb = callback
    let opts = options
    if (typeof options === 'function') {
      cb = options
      opts = {}
    }

    if (hostname === 'newsportal-cms.ddev.site') {
      const isAll = typeof opts === 'object' && opts !== null && Boolean(opts.all)
      return process.nextTick(() => {
        if (typeof cb === 'function') {
          if (isAll) {
            cb(null, [{ address: '127.0.0.1', family: 4 }])
          } else {
            cb(null, '127.0.0.1', 4)
          }
        }
      })
    }

    return origLookup.call(dns, hostname, opts, cb)
  }) as typeof dns.lookup
})

