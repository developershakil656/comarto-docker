import { useHead } from '@unhead/vue'

export function useSEO() {
  const setMetaTags = (title, description, image = null, keywords = null) => {
    const metaTags = [
      { name: 'description', content: description },
      { property: 'og:title', content: title },
      { property: 'og:description', content: description },
      { property: 'og:type', content: 'website' },
      { property: 'og:url', content: window.location.href },
      { property: 'twitter:card', content: 'summary_large_image' },
      { property: 'twitter:title', content: title },
      { property: 'twitter:description', content: description }
    ]

    const imageToUse = image || '/logo.svg';
    metaTags.push(
      { property: 'og:image', content: imageToUse },
      { property: 'twitter:image', content: imageToUse }
    )

    if (keywords) {
      metaTags.push({ name: 'keywords', content: keywords })
    }

    useHead({
      title: title,
      meta: metaTags,
      link: [
        {
          rel: 'canonical',
          href: window.location.href
        }
      ]
    })
  }

  return { setMetaTags }
}