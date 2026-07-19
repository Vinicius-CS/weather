<script setup>
import { nextTick, watch, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { getSearches, getCities, getWeather } from './services/api.js'
import CurrentWeather from './components/CurrentWeather.vue';
import ForecastList from './components/ForecastList.vue';

const { t } = useI18n()

const current = ref(null)
const forecast = ref(null)
const userLocation = ref(null)
const searchInput = ref(null)
const loading = ref(true)
const cities = ref([])
const topCities = ref([])
const search = ref('')
const geoText = ref('')

async function loadCity(city)
{
  if (!city.trim())
  {
    return
  }

  try
  {
    loading.value = true
    search.value = ''
    geoText.value = ''

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
  catch (e)
  {
    console.error(e)
  }
  finally
  {
    loading.value = false
  }
}

watch(loading, async (isLoading) => {
  if (!isLoading)
  {
    await nextTick()
    searchInput.value?.focus()
  }
})

onMounted(async () => {
  window.addEventListener('keydown', (event) => {
    if (loading.value)
    {
      return
    }

    if (event.key === 'Enter')
    {
      if (search.value.trim())
      {
        loadCity(search.value)
      }
      else
      {
        searchInput.value?.focus()
      }
    }
    else if (event.key.length === 1 && !event.ctrlKey && !event.metaKey)
    {
      searchInput.value?.focus()
    }
  })

  topCities.value = await getSearches() ?? []

  if (!navigator.geolocation || !window.isSecureContext)
  {
    loading.value = false
    return
  }

  geoText.value = t('geo.locating')

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
        geoText.value = t('geo.denied')
      }
      finally
      {
        loading.value = false
      }
    },
    () => {
      loading.value = false
      geoText.value = t('geo.denied')
    }
  )
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
        ref="searchInput"
        v-model="search"
        :placeholder="$t('search.placeholder')"
        type="text"
        autofocus
        :disabled="loading"
        @keydown.enter.prevent="loadCity(search)"
      />
    </div>

    <div
      class="loading-bar"
      :class="{ hidden: !loading }"
    >
      <div />
    </div>

    <div v-if="topCities.length" class="chips">
      <span>{{ t('top.title') }}</span>
      <button
        v-for="top in topCities"
        :key="top.city"
        :disabled="loading"
        @click="loadCity(top.city)"
      >
        {{ top.city }}
      </button>
    </div>

    <p
      v-if="geoText && !current"
      class="geo-status"
    >
      {{ geoText }}
    </p>

    <template v-if="current">
      <CurrentWeather
        :key="current.city"
        :current="current"
      />

      <ForecastList
        v-if="forecast"
        :key="current.city"
        :forecast="forecast"
      />
    </template>
  </main>
</template>