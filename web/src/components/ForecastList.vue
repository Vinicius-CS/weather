<script setup>
import { ref, computed, watch, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
  forecast: { type: Object, required: true },
})

const { locale } = useI18n()

const days = computed(() => {
  const today = new Date().toISOString().slice(0, 10)
  const groups = {}

  for (const item of props.forecast.list)
  {
    const date = item.date.slice(0, 10)

    if (date !== today)
    {
      if (!groups[date])
      {
        groups[date] = []
      }

      groups[date].push(item)
    }
  }

  return Object.entries(groups).slice(0, 5).map(([date, items]) => {
    const midday = items.find((i) => i.date.includes('12:00:00')) ?? items[0]

    return {
      date,
      label: new Date(date + 'T12:00:00').toLocaleDateString(locale.value, {
        weekday: 'short',
        day: '2-digit',
        month: '2-digit',
      }),
      icon: `https://cdn.jsdelivr.net/npm/@bybas/weather-icons@2.0.0/production/fill/openweathermap/${midday.icon}.svg`,
      description: midday.description,
      min: Math.round(Math.min(...items.map((i) => i.temp_min))),
      max: Math.round(Math.max(...items.map((i) => i.temp_max))),
    }
  })
})

const progress = ref(0)
let frame = 0

function animate()
{
  cancelAnimationFrame(frame)

  const duration = 1400
  const start = performance.now()

  function step(now)
  {
    const progressStep = Math.min((now - start) / duration, 1)
    progress.value = 1 - Math.pow(1 - progressStep, 3)

    if (progressStep < 1)
    {
      frame = requestAnimationFrame(step)
    }
  }

  frame = requestAnimationFrame(step)
}

watch(() => props.forecast, animate, { immediate: true })
onUnmounted(() => cancelAnimationFrame(frame))
</script>

<template>
  <section class="card">
    <h3>{{ $t('forecast.title') }}</h3>

    <ul class="forecast">
      <li v-for="day in days" :key="day.date">
        <span class="day">{{ day.label }}</span>
        <img :src="day.icon" :alt="day.description" />
        <span class="description">{{ day.description }}</span>
        <span class="temps"><strong>{{ Math.round(day.max * progress) }}°</strong> / {{ Math.round(day.min * progress) }}°</span>
      </li>
    </ul>
  </section>
</template>