import { createI18n } from 'vue-i18n'
import ptBR from './locales/pt-BR.json'
import en from './locales/en.json'

export const API_LANG = {
  'pt-BR': 'pt_br',
  en: 'en',
}

export default createI18n({
  locale: localStorage.getItem('weather-locale') || navigator.language?.startsWith('pt') ? 'pt-BR' : 'en',
  fallbackLocale: 'en',
  messages: {
    'pt-BR': ptBR,
    en,
  },
})
