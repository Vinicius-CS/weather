<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { getCities, getWeather } from './services/api.js'
import CurrentWeather from './components/CurrentWeather.vue';
import ForecastList from './components/ForecastList.vue';

const { t } = useI18n()

const current = ref(null)
const forecast = ref(null)
const cities = ref([])

async function loadCity(city) {
  cities.value = await getCities(city)

  if (cities.value.length === 0) {
    return
  }

  current.value = await getWeather(
    'weather',
    cities.value[0].latitude,
    cities.value[0].longitude
  )

  forecast.value = await getWeather(
    'forecast',
    cities.value[0].latitude,
    cities.value[0].longitude
  )
}
</script>

<template>
  <main class="container">
    <header class="header">
      <h1>{{ t('header.title') }}</h1>
      <p>{{ t('header.subtitle') }}</p>
    </header>

    <div class="search">
      <input
        v-model="search"
        :placeholder="$t('search.placeholder')"
        type="text"
        autofocus
        @keydown.enter.prevent="loadCity(search)"
      />
    </div>

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