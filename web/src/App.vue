<script setup>
import { nextTick, watch, onMounted, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import { getSearches, getCities, getWeather } from './services/api.js'
import { formatLocation } from './services/location.js'
import CurrentWeather from './components/CurrentWeather.vue';
import ForecastList from './components/ForecastList.vue';

const { t, locale } = useI18n()

const current = ref(null)
const forecast = ref(null)
const searchInput = ref(null)
const loading = ref(true)
const autocomplete = ref([])
const topCities = ref([])
const search = ref('')
const geoText = ref('')

async function loadByCoordinates(latitude, longitude)
{
  try
  {
    loading.value = true
    autocomplete.value = []
    search.value = ''
    geoText.value = ''

    current.value = await getWeather(
      'weather',
      latitude,
      longitude
    )

    forecast.value = await getWeather(
      'forecast',
      latitude,
      longitude
    )

    topCities.value = await getSearches() ?? []
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

async function loadCity(name)
{
  const results = await getCities(name.trim()) ?? []

  if (results.length)
  {
    loadByCoordinates(results[0].latitude, results[0].longitude)
  }
}

watch(search, async (text) => {
  if (text.trim().length < 1)
  {
    autocomplete.value = []
    return
  }

  autocomplete.value = await getCities(text.trim()) ?? []
})

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

  getSearches().then((rows) => { topCities.value = rows ?? [] })

  if (!navigator.geolocation || !window.isSecureContext)
  {
    loading.value = false
    return
  }

  geoText.value = t('geo.locating')

  navigator.geolocation.getCurrentPosition(
    (position) => loadByCoordinates(
      position.coords.latitude.toFixed(4),
      position.coords.longitude.toFixed(4)
    ),
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
        @blur="autocomplete = []"
      />

      <ul v-if="autocomplete.length" class="autocomplete">
        <li
          v-for="city in autocomplete"
          :key="`${city.latitude},${city.longitude}`"
          @mousedown="loadByCoordinates(city.latitude, city.longitude)"
        >
          {{ formatLocation(city.name, city.state, city.country, locale) }}
        </li>
      </ul>
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