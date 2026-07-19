import i18n, { API_LANG } from '../i18n.js'

async function request(path)
{
  const lang = API_LANG[typeof i18n.global.locale === 'string' ? i18n.global.locale : i18n.global.locale.value] ?? 'en'
  const res = await fetch(`${path}${path.includes('?') ? '&' : '?'}lang=${lang}`)
  const data = await res.json().catch(() => ({}))

  if (!res.ok)
  {
    throw new Error(data.error || 'Erro ao consultar a API')
  }

  return data
}

export function getSearches()
{
  return request('/api/searches')
}

export function getCities(text)
{
  const params = {
    'text': text
  }

  return request(`/api/cities?${new URLSearchParams(params)}`)
}

export function getWeather(type, latitude, longitude)
{
  const params = new URLSearchParams({
    'type': type,
    'latitude': latitude,
    'longitude': longitude
  })

  return request(`/api/weather?${params}`)
}
