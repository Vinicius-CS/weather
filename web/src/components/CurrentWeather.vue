<script setup>
import { computed } from 'vue'
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
</script>

<template>
  <section class="card">
    <div class="current">
      <img
        :src="`https://openweathermap.org/img/wn/${current.icon}@4x.png`"
        :alt="current.description"
      />

      <div>
        <h2>{{ location }}</h2>
        <p class="temp">{{ Math.round(current.temp) }}°C</p>
        <p class="description">{{ current.description }}</p>
      </div>
    </div>

    <ul class="details">
      <li>
        <span>{{ $t('current.feelsLike') }}</span>
        <strong>{{ Math.round(current.feels_like) }}°C</strong>
      </li>

      <li>
        <span>{{ $t('current.maxMin') }}</span>
        <strong>{{ Math.round(current.temp_max) }}° / {{ Math.round(current.temp_min) }}°</strong>
      </li>

      <li>
        <span>{{ $t('current.humidity') }}</span>
        <strong>{{ current.humidity }}%</strong>
      </li>

      <li>
        <span>{{ $t('current.wind') }}</span>
        <strong>{{ Math.round(current.wind * 3.6) }} km/h</strong>
      </li>
    </ul>
  </section>
</template>