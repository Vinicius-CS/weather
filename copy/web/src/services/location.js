export function formatLocation(name, state, country, locale)
{
  const place = `${name}${state ? `, ${state}` : ''}`
  const region = country ? new Intl.DisplayNames([locale], { type: 'region' }).of(country) : ''

  return region ? `${place} - ${region}` : place
}
