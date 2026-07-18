<script setup>
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { getCities, getWeather } from './services/api.js'
import CurrentWeather from './components/CurrentWeather.vue';
import ForecastList from './components/ForecastList.vue';

const { t } = useI18n()

const current = ref(null)
const forecast = ref(null)
const cities = ref([])

onMounted(async () => {
  cities.value = await getCities({
    text: 'florianopolis'
  })

  current.value = await getWeather({
    'type': 'weather',
    'latitude': cities.value[0].latitude,
    'longitude': cities.value[0].longitude
  })

  forecast.value = await getWeather({
    'type': 'forecast',
    'latitude': cities.value[0].latitude,
    'longitude': cities.value[0].longitude
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