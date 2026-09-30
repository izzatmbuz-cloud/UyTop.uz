import { computed, ref } from 'vue';

const locale = ref('uz');
let initialized = false;

const messages = {
  uz: {
    catalog: 'Katalog', compare: 'Solishtirish', projects: 'Yangi qurilishlar', cabinet: 'Kabinet', moderation: 'Moderatsiya', login: 'Kirish', publish: 'E’lon joylashtirish', profile: 'Profil', listings: 'E’lonlarim', requests: 'Murojaatlarim', messages: 'Habarlar', logout: 'Chiqish', home: 'Bosh sahifa', themeDark: 'Tungi rejim', themeLight: 'Kunduzgi rejim',
    heroBadge: 'Andijon · tekshirilgan shartlar', hero1: 'Uy emas,', hero2: 'mos joy', hero3: 'qidiring.', heroText: 'Talabalar qabul qilinadimi, qancha pul bilan joylashish kerak va nechta bo‘sh o‘rin bor — qo‘ng‘iroq qilishdan oldin bilib oling.', viewHomes: 'Uy-joylarni ko‘rish', compareVariants: 'Variantlarni solishtirish', quickSearch: 'Tez qidiruv', suitable: 'Sizga nima mos?', district: 'Hudud', allDistricts: 'Barcha hududlar', maxPrice: 'Eng ko‘p narx', currency: 'Valyuta', students: 'Talabalar qabul qilinadi', find: 'Mos takliflarni topish', whole: 'Butun uy', room: 'Xona', bed: 'O‘rin', all: 'Barchasi',
  },
  ru: {
    catalog: 'Каталог', compare: 'Сравнение', projects: 'Новостройки', cabinet: 'Кабинет', moderation: 'Модерация', login: 'Войти', publish: 'Разместить объявление', profile: 'Профиль', listings: 'Мои объявления', requests: 'Мои обращения', messages: 'Сообщения', logout: 'Выйти', home: 'Главная', themeDark: 'Тёмная тема', themeLight: 'Светлая тема',
    heroBadge: 'Андижан · понятные условия', hero1: 'Ищите не дом,', hero2: 'а своё место', hero3: 'для жизни.', heroText: 'Узнайте заранее, принимают ли студентов, сколько нужно для заселения и сколько свободных мест осталось.', viewHomes: 'Посмотреть жильё', compareVariants: 'Сравнить варианты', quickSearch: 'Быстрый поиск', suitable: 'Что вам подходит?', district: 'Район', allDistricts: 'Все районы', maxPrice: 'Максимальная цена', currency: 'Валюта', students: 'Принимают студентов', find: 'Найти подходящие варианты', whole: 'Весь дом', room: 'Комната', bed: 'Место', all: 'Все варианты',
  },
};

function initialize() {
  if (initialized || typeof window === 'undefined') return;
  initialized = true;
  locale.value = window.localStorage.getItem('uytop-locale') === 'ru' ? 'ru' : 'uz';
  document.documentElement.lang = locale.value;
}

export function useLocale() {
  initialize();
  const t = (key) => messages[locale.value]?.[key] || messages.uz[key] || key;
  const nextLocale = computed(() => locale.value === 'uz' ? 'RU' : 'UZ');
  function toggleLocale() { locale.value = locale.value === 'uz' ? 'ru' : 'uz'; window.localStorage.setItem('uytop-locale', locale.value); document.documentElement.lang = locale.value; }
  return { locale, nextLocale, t, toggleLocale };
}
