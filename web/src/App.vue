<script setup>
import { onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { getSearches, getCities, getWeather } from './services/api.js'
import CurrentWeather from './components/CurrentWeather.vue';
import ForecastList from './components/ForecastList.vue';

const { t } = useI18n()

const current = ref(null)
const forecast = ref(null)
const userLocation = ref(null)
const cities = ref([])
const topCities = ref([])
const search = ref('')

async function loadCity(city)
{
  search.value = ''

  cities.value = await getCities(city.trim()) ?? []

  if (cities.value.length === 0)
  {
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

onMounted(async () => {
  topCities.value = await getSearches() ?? []

  if (!navigator.geolocation || !window.isSecureContext)
  {
    return
  }

  navigator.geolocation.getCurrentPosition(
    async (position) => {
    try
    {
      userLocation.value = {
        latitude: position.coords.latitude.toFixed(4),
        longitude: position.coords.longitude.toFixed(4),
      }

      current.value = await getWeather(
        'weather',
        userLocation.value.latitude,
        userLocation.value.longitude
      )

      forecast.value = await getWeather(
        'forecast',
        userLocation.value.latitude,
        userLocation.value.longitude
      )
    }
    catch (e)
    {
      console.error(e)
    }
  }, (error) => {
    console.error(error)
  })
})
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

    <div v-if="topCities.length" class="chips">
      <span>{{ t('top.title') }}</span>
      <button v-for="top in topCities" :key="top.city" class="chip" @click="loadCity(top.city)">
        {{ top.city }}
      </button>
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