<script setup>
import { computed } from 'vue'
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
    const date = item.dt_txt.slice(0, 10)

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
    const midday = items.find((i) => i.dt_txt.includes('12:00:00')) ?? items[0]

    return {
      date,
      label: new Date(date + 'T12:00:00').toLocaleDateString(locale.value, {
        weekday: 'short',
        day: '2-digit',
        month: '2-digit',
      }),
      icon: `https://openweathermap.org/img/wn/${midday.weather[0].icon}@2x.png`,
      description: midday.weather[0].description,
      min: Math.round(Math.min(...items.map((i) => i.main.temp_min))),
      max: Math.round(Math.max(...items.map((i) => i.main.temp_max))),
    }
  })
})
</script>

<template>
  <section class="card">
    <h3>{{ $t('forecast.title') }}</h3>

    <ul class="forecast">
      <li v-for="day in days" :key="day.date">
        <span class="day">{{ day.label }}</span>
        <img :src="day.icon" :alt="day.description" />
        <span class="description">{{ day.description }}</span>
        <span class="temps"><strong>{{ day.max }}°</strong> / {{ day.min }}°</span>
      </li>
    </ul>
  </section>
</template>