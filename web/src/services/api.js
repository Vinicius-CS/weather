async function request(path)
{
  const res = await fetch(path)
  const data = await res.json().catch(() => ({}))

  if (!res.ok)
  {
    throw new Error(data.error || 'Erro ao consultar a API')
  }

  return data
}

export function getWeather(params)
{
  return request(`/api/weather?${new URLSearchParams(params)}`)
}