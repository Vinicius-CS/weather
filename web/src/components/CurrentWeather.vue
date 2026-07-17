<script setup>
defineProps({
  current: { type: Object, required: true },
})
</script>

<template>
  <section class="card">
    <div class="current">
      <img
        :src="`https://openweathermap.org/img/wn/${current.weather[0].icon}@4x.png`"
        :alt="current.weather[0].description"
      />
      <div>
        <h2>{{ current.name }}, {{ current.state || current.sys.country }}</h2>
        <p class="temp">{{ Math.round(current.main.temp) }}°C</p>
        <p class="description">{{ current.weather[0].description }}</p>
      </div>
    </div>

    <ul class="details">
      <li>
        <span>{{ $t('current.feelsLike') }}</span>
        <strong>{{ Math.round(current.main.feels_like) }}°C</strong>
      </li>
      <li>
        <span>{{ $t('current.minMax') }}</span>
        <strong>{{ Math.round(current.main.temp_min) }}° / {{ Math.round(current.main.temp_max) }}°</strong>
      </li>
      <li>
        <span>{{ $t('current.humidity') }}</span>
        <strong>{{ current.main.humidity }}%</strong>
      </li>
      <li>
        <span>{{ $t('current.wind') }}</span>
        <strong>{{ Math.round(current.wind.speed * 3.6) }} km/h</strong>
      </li>
    </ul>
  </section>
</template>