<script setup>
import { reactive, computed, watch, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  current: { type: Object, required: true },
})

const { locale } = useI18n()

const location = computed(() => {
  const place = `${props.current.city}${props.current.state ? `, ${props.current.state}` : ''}`
  const country = props.current.country ? new Intl.DisplayNames([locale.value], { type: 'region' }).of(props.current.country) : ''

  return country ? `${place} - ${country}` : place
})

const values = reactive({
  temp: 0,
  feelsLike: 0,
  tempMin: 0,
  tempMax: 0,
  humidity: 0,
  wind: 0,
})

let frame = 0

function animate(targets)
{
  cancelAnimationFrame(frame)

  const duration = 1400
  const start = performance.now()

  function step(now)
  {
    const progress = Math.min((now - start) / duration, 1)
    const progressTarget = 1 - Math.pow(1 - progress, 3)

    for (const key in targets)
    {
      values[key] = Math.round(targets[key] * progressTarget)
    }

    if (progress < 1)
    {
      frame = requestAnimationFrame(step)
    }
  }

  frame = requestAnimationFrame(step)
}

watch(
  () => props.current,
  (current) => animate({
    temp: current.temp,
    feelsLike: current.feels_like,
    tempMin: current.temp_min,
    tempMax: current.temp_max,
    humidity: current.humidity,
    wind: current.wind * 3.6,
  }),
  { immediate: true }
)

onUnmounted(() => cancelAnimationFrame(frame))
</script>

<template>
  <section class="card">
    <div class="current">
      <img
        :src="`https://cdn.jsdelivr.net/npm/@bybas/weather-icons@2.0.0/production/fill/openweathermap/${current.icon}.svg`"
        :alt="current.description"
      />

      <div>
        <h2>{{ location }}</h2>
        <p class="temp">{{ values.temp }}°C</p>
        <p class="description">{{ current.description }}</p>
      </div>
    </div>

    <ul class="details">
      <li>
        <span>{{ $t('current.feelsLike') }}</span>
        <strong>{{ values.feelsLike }}°C</strong>
      </li>

      <li>
        <span>{{ $t('current.maxMin') }}</span>
        <strong>{{ values.tempMax }}° / {{ values.tempMin }}°</strong>
      </li>

      <li>
        <span>{{ $t('current.humidity') }}</span>
        <strong>{{ values.humidity }}%</strong>
      </li>

      <li>
        <span>{{ $t('current.wind') }}</span>
        <strong>{{ values.wind }} km/h</strong>
      </li>
    </ul>
  </section>
</template>