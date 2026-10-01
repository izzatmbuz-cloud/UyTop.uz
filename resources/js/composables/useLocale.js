import { computed, ref } from 'vue';

const locale = ref('uz');
let initialized = false;

const messages = {
  uz: {
    catalog: 'Katalog', compare: 'Solishtirish', projects: 'Yangi qurilishlar', cabinet: 'Kabinet', moderation: 'Moderatsiya', login: 'Kirish', publish: 'E’lon joylashtirish', profile: 'Profil', listings: 'E’lonlarim', requests: 'Murojaatlarim', messages: 'Habarlar', logout: 'Chiqish', home: 'Bosh sahifa', themeDark: 'Tungi rejim', themeLight: 'Kunduzgi rejim',
    heroBadge: 'Andijon · tekshirilgan shartlar', hero1: 'Uy emas,', hero2: 'mos joy', hero3: 'qidiring.', heroText: 'Talabalar qabul qilinadimi, qancha pul bilan joylashish kerak va nechta kishilik joy bo‘sh — qo‘ng‘iroq qilishdan oldin bilib oling.', viewHomes: 'Uy-joylarni ko‘rish', compareVariants: 'Variantlarni solishtirish', quickSearch: 'Tez qidiruv', suitable: 'Qanday uy-joy kerak?', district: 'Tuman yoki shahar', allDistricts: 'Barcha hududlar', maxPrice: 'Eng yuqori narx', currency: 'Valyuta', students: 'Faqat talabalarni qabul qiladiganlar', find: 'Mos uy-joylarni ko‘rsatish', whole: 'Butun uy', wholeHint: 'Uyning hammasini ijaraga olish', room: 'Alohida xona', roomHint: 'Boshqalar bilan bir uyda, alohida xona', bed: 'Bir kishilik joy', bedHint: 'Xona yoki uyda bir kishi uchun joy', all: 'Farqi yo‘q', allHint: 'Barcha ijara turlarini ko‘rsatish',
  },
  ru: {
    catalog: 'Каталог', compare: 'Сравнение', projects: 'Новостройки', cabinet: 'Кабинет', moderation: 'Модерация', login: 'Войти', publish: 'Разместить объявление', profile: 'Профиль', listings: 'Мои объявления', requests: 'Мои обращения', messages: 'Сообщения', logout: 'Выйти', home: 'Главная', themeDark: 'Тёмная тема', themeLight: 'Светлая тема',
    heroBadge: 'Андижан · понятные условия', hero1: 'Ищите не дом,', hero2: 'а своё место', hero3: 'для жизни.', heroText: 'Заранее узнайте, принимают ли студентов, сколько нужно для заселения и на сколько человек осталось место.', viewHomes: 'Посмотреть жильё', compareVariants: 'Сравнить варианты', quickSearch: 'Быстрый поиск', suitable: 'Какое жильё вам нужно?', district: 'Район или город', allDistricts: 'Все районы', maxPrice: 'Цена не выше', currency: 'Валюта', students: 'Только варианты для студентов', find: 'Показать подходящее жильё', whole: 'Весь дом', wholeHint: 'Снять жильё целиком', room: 'Отдельная комната', roomHint: 'Отдельная комната в общей квартире', bed: 'Место для одного', bedHint: 'Одно место в комнате или квартире', all: 'Неважно', allHint: 'Показать все варианты аренды',
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
