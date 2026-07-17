<script setup>
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { getWeather } from './services/api.js'
import CurrentWeather from './components/CurrentWeather.vue';
import ForecastList from './components/ForecastList.vue';

const { t } = useI18n()

const current = ref(null)
const forecast = ref(null)

onMounted(async () => {
  current.value = await getWeather({
    'type': 'weather',
    'latitude': '-27.59',
    'longitude': '-48.55'
  })

  forecast.value = await getWeather({
    'type': 'forecast',
    'latitude': '-27.59',
    'longitude': '-48.55'
  })
})
</script>

<template>
  <main class="container">
    <header class="header">
      <h1>{{ t('header.title') }}</h1>
      <p>{{ t('header.subtitle') }}</p>
    </header>

    <template v-if="current">
      <CurrentWeather
        :current="current"
      />
      <ForecastList
        v-if="forecast"
        :forecast="forecast"
      />
    </template>
  </main>
</template>