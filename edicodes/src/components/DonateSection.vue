<template>
  <section id="donate" class="donation-section" dir="rtl" aria-labelledby="donation-title">
    <div class="donation-shell">
      <div class="donation-layout">
        <header class="donation-intro">
          <div class="heart-art" aria-hidden="true">
            <div class="heart-orbit orbit-one" />
            <div class="heart-orbit orbit-two" />
            <div class="heart-tile"><font-awesome-icon icon="heart" /></div>
            <span class="art-spark spark-one">+</span><span class="art-spark spark-two">+</span>
          </div>
          <h2 id="donation-title">حمایت شما<br /><span>انگیزه‌ای برای ساختن.</span></h2>
          <p class="intro-copy">
            اگر آموزش‌ها و پروژه‌هایم برایتان مفید بوده، با حمایت شما زمان بیشتری برای ساختن و
            به‌اشتراک‌گذاشتن دانش خواهم داشت.
          </p>
          <div class="impact-list">
            <div>
              <span class="impact-icon"><font-awesome-icon icon="code" /></span>
              <div>
                <h3>پروژه‌های متن‌باز</h3>
                <p>ابزارهایی برای استفاده و یادگیری همه</p>
              </div>
            </div>
            <div>
              <span class="impact-icon"><font-awesome-icon icon="book" /></span>
              <div>
                <h3>آموزش و تجربه‌های واقعی</h3>
                <p>محتوای کاربردی از مسیر توسعه نرم‌افزار</p>
              </div>
            </div>
          </div>
          <p class="personal-note">ممنونم که بخشی از این مسیر هستید.</p>
        </header>

        <form class="donation-form" @submit.prevent="handleDonate" :aria-busy="isLoading">
          <div class="form-heading">
            <div>
              <span class="eyebrow">حمایت مالی</span>
              <h3>سهم شما در ادامه‌ی مسیر</h3>
            </div>
            <span class="form-heart" aria-hidden="true"><font-awesome-icon icon="heart" /></span>
          </div>
          <fieldset :disabled="isLoading">
            <legend>مبلغ حمایت را انتخاب کنید</legend>
            <div class="amount-options">
              <button
                v-for="preset in presets"
                :key="preset"
                type="button"
                class="amount-option"
                :class="{ selected: Number(amount) === preset }"
                :aria-pressed="Number(amount) === preset"
                @click="selectAmount(preset)"
              >
                <span class="option-indicator" aria-hidden="true"
                  ><font-awesome-icon v-if="Number(amount) === preset" icon="check"
                /></span>
                <strong>{{ formatAmount(preset) }}</strong
                ><span class="currency">تومان</span>
              </button>
            </div>
            <label class="field-label" for="donation-amount">یا مبلغ دلخواه شما</label>
            <div class="amount-input-wrap">
              <input
                id="donation-amount"
                v-model="amount"
                type="number"
                inputmode="numeric"
                min="1000"
                step="any"
                required
                aria-describedby="amount-help amount-error"
                :aria-invalid="!!amountError"
                @input="validateAmount"
              /><span>تومان</span>
            </div>
            <p id="amount-help" class="field-help">هر مبلغی ارزشمند است؛ حداقل ۱٬۰۰۰ تومان.</p>
            <p id="amount-error" class="field-error" aria-live="polite">{{ amountError }}</p>
            <div class="details-heading"><span>چند کلمه از شما</span><span>اختیاری</span></div>
            <div class="identity-fields">
              <div>
                <label class="field-label" for="donation-name">نام شما</label
                ><input
                  id="donation-name"
                  v-model="name"
                  type="text"
                  autocomplete="name"
                  placeholder="نام یا نام مستعار"
                />
              </div>
              <div>
                <label class="field-label" for="donation-email">ایمیل</label
                ><input
                  id="donation-email"
                  v-model="email"
                  type="email"
                  autocomplete="email"
                  placeholder="you@example.com"
                  dir="ltr"
                />
              </div>
            </div>
            <label class="field-label message-label" for="donation-message">پیام شما</label>
            <textarea
              id="donation-message"
              v-model="message"
              rows="2"
              placeholder="خواندن پیامتان خوشحالم می‌کند…"
            />
          </fieldset>
          <Transition name="notice"
            ><div v-if="error" class="payment-error" role="alert">
              <font-awesome-icon icon="exclamation-circle" /> {{ error }}
            </div></Transition
          >
          <div v-if="showSuccess" class="payment-success" role="status">{{ successMessage }}</div>
          <button class="payment-button" type="submit" :disabled="!isValidAmount || isLoading">
            <font-awesome-icon
              :icon="isLoading ? 'circle-notch' : 'heart'"
              :class="{ spinner: isLoading }"
            />
            <span>{{
              isLoading
                ? 'در حال اتصال به درگاه…'
                : 'حمایت با ' + formatAmount(Number(amount) || 0) + ' تومان'
            }}</span>
            <font-awesome-icon v-if="!isLoading" icon="arrow-left" class="payment-arrow" />
          </button>
          <p class="payment-note">
            <font-awesome-icon icon="lock" /> برای پرداخت به درگاه منتقل می‌شوید
          </p>
        </form>
      </div>

      <div class="supporters-section">
        <header class="supporters-heading">
          <div>
            <span class="eyebrow">همراهان این مسیر</span>
            <h3>با حمایت شما ساخته می‌شود<span aria-hidden="true"> ♥</span></h3>
          </div>
          <p>قدردان تک‌تک شما هستم.</p>
        </header>
        <div v-if="donorsLoading" class="supporters-state" role="status">
          <font-awesome-icon icon="circle-notch" class="spinner" />
          <p>در حال دریافت حامیان…</p>
        </div>
        <div v-else-if="donorsError && !donors.length" class="supporters-state" role="status">
          <p>دریافت فهرست حامیان ممکن نشد.</p>
          <button type="button" class="secondary-button" @click="fetchDonors()">تلاش دوباره</button>
        </div>
        <div v-else-if="!donors.length" class="supporters-state">
          <font-awesome-icon icon="heart" />
          <p>جای اولین همراه اینجا خالی‌ست.</p>
          <span>حمایت شما می‌تواند شروع این مسیر باشد.</span>
        </div>
        <div v-else class="supporters-grid">
          <article v-for="donor in donors" :key="donor.id" class="supporter-card">
            <div class="supporter-top">
              <span class="supporter-avatar" aria-hidden="true">{{
                Array.from(donor.name || 'حامی')[0]
              }}</span>
              <div class="supporter-person">
                <h4>{{ donor.name || 'حامی ناشناس' }}</h4>
                <p>{{ donor.created_at_human }}</p>
              </div>
              <span class="supporter-amount">{{ donor.formatted_amount }}</span>
            </div>
            <p v-if="donor.message" class="supporter-message">{{ donor.message }}</p>
          </article>
        </div>
        <p v-if="donorsError && donors.length" class="field-error" role="alert">
          دریافت حامیان بیشتر ممکن نشد. دوباره تلاش کنید.
        </p>
        <div v-if="hasMoreDonors && donors.length" class="load-more">
          <button
            type="button"
            class="secondary-button"
            :disabled="loadingMore"
            @click="loadMoreDonors"
          >
            <font-awesome-icon
              :icon="loadingMore ? 'circle-notch' : 'chevron-down'"
              :class="{ spinner: loadingMore }"
            />
            {{ loadingMore ? 'در حال بارگذاری…' : 'نمایش حامیان بیشتر' }}
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const amount = ref(250000)
const name = ref('')
const email = ref('')
const message = ref('')
const isLoading = ref(false)
const error = ref('')
const amountError = ref('')
const presets = [50000, 250000, 1000000]
const formatAmount = (value) => new Intl.NumberFormat('fa-IR').format(value)
const showSuccess = ref(false)
const successMessage = ref('')

// Donors state
const donors = ref([])
const donorsLoading = ref(false)
const donorsError = ref(false)
const currentPage = ref(1)
const hasMoreDonors = ref(false)
const loadingMore = ref(false)

const isValidAmount = computed(() => {
  return Number.isFinite(Number(amount.value)) && Number(amount.value) >= 1000 && !amountError.value
})

const selectAmount = (value) => {
  amount.value = value
  amountError.value = ''
}

const validateAmount = () => {
  amountError.value = ''

  if (!amount.value) {
    amountError.value = 'لطفاً مبلغ را وارد کنید'
    return
  }

  if (Number(amount.value) < 1000) {
    amountError.value = 'حداقل مبلغ ۱۰۰۰ تومان است'

    return
  }
}

const handleDonate = async () => {
  if (!isValidAmount.value || isLoading.value) return

  error.value = ''
  showSuccess.value = false
  isLoading.value = true

  try {
    const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'https://api.edrisranjbar.ir'
    const response = await fetch(`${apiBaseUrl}/donations/pay`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        amount: amount.value,
        name: name.value,
        email: email.value,
        message: message.value,
      }),
    })

    const data = await response.json()

    if (response.ok && data.success) {
      // Store donation ID in localStorage for future reference if needed
      if (data.donation_id) {
        localStorage.setItem('current_donation_id', data.donation_id)
      }

      // Redirect to payment gateway
      window.location.href = data.payment_url
    } else {
      error.value = data.message || 'خطا در اتصال به درگاه پرداخت. لطفاً مجدد تلاش کنید.'

      if (data.errors && data.errors.amount) {
        amountError.value = data.errors.amount[0]
      }
    }
  } catch (err) {
    error.value = 'خطا در اتصال به سرور. لطفاً مجدد تلاش کنید.'
    console.error('Donation error:', err)
  } finally {
    isLoading.value = false
  }
}

// Fetch donors from API
const fetchDonors = async (page = 1) => {
  try {
    if (page === 1) {
      donorsLoading.value = true
    } else {
      loadingMore.value = true
    }
    donorsError.value = false

    const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'https://api.edrisranjbar.ir'
    const response = await fetch(`${apiBaseUrl}/donations?per_page=8&page=${page}`)

    if (!response.ok) {
      throw new Error('Failed to fetch donors')
    }

    const result = await response.json()

    if (result.success && result.data) {
      if (page === 1) {
        donors.value = result.data.data
      } else {
        donors.value = [...donors.value, ...result.data.data]
      }

      currentPage.value = result.data.current_page
      hasMoreDonors.value = result.data.current_page < result.data.last_page
    }
  } catch (err) {
    console.error('Error fetching donors:', err)
    donorsError.value = true
  } finally {
    donorsLoading.value = false
    loadingMore.value = false
  }
}

// Load more donors
const loadMoreDonors = () => {
  fetchDonors(currentPage.value + 1)
}

// Fetch donors on mount
onMounted(() => {
  fetchDonors()
})
</script>

<style scoped>
.donation-section {
  --surface: #171c2a;
  --ink: #f5f6fc;
  --muted: #b4bdcf;
  --accent: #b7c4ff;
  --line: #63708a;
  background: radial-gradient(ellipse at 90% 15%, #232b493d, transparent 45%), #10141f;
  color: var(--ink);
  padding: 88px 24px 72px;
  scroll-margin-top: 72px;
  overflow: hidden;
}
.donation-shell {
  max-width: 1120px;
  margin: auto;
}
.donation-layout {
  display: grid;
  grid-template-columns: 1fr 1.08fr;
  gap: 88px;
  align-items: center;
}
.donation-intro {
  animation: reveal 650ms cubic-bezier(0.2, 0, 0, 1) both;
}
.eyebrow {
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 0.02em;
  color: var(--accent);
  display: inline-flex;
  align-items: center;
  gap: 9px;
}
.status-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--accent);
  box-shadow: 0 0 0 5px #b7c4ff0c;
}
.donation-intro h2 {
  font-size: clamp(30px, 3.2vw, 42px);
  font-weight: 800;
  line-height: 1.8;
  letter-spacing: -1.3px;
  margin: 12px 0 16px;
}
.donation-intro h2 span {
  color: var(--accent);
}
.intro-copy {
  font-size: 15px;
  line-height: 2.2;
  color: var(--muted);
  max-width: 420px;
}
.heart-art {
  position: relative;
  width: 168px;
  height: 148px;
  display: grid;
  place-items: center;
  margin: 28px 0 12px;
}
.heart-tile {
  width: 78px;
  height: 78px;
  border: 1px solid #859be880;
  border-radius: 25px;
  background: linear-gradient(145deg, #354166, #20283e);
  box-shadow:
    0 16px 50px #05081370,
    inset 0 1px 0 #c2cdff24;
  display: grid;
  place-items: center;
  color: #c4ceff;
  font-size: 31px;
  transform: rotate(-9deg);
  animation: heart-arrive 850ms cubic-bezier(0.2, 0, 0, 1) both;
}
.heart-orbit {
  position: absolute;
  width: 138px;
  height: 138px;
  border: 1px solid #aabaff18;
  border-radius: 50%;
}
.orbit-two {
  width: 104px;
  height: 104px;
  border-style: dashed;
  animation: orbit-reveal 1400ms ease-out both;
}
.art-spark {
  position: absolute;
  color: #9faedf;
  font-size: 20px;
}
.spark-one {
  top: 13px;
  right: 15px;
}
.spark-two {
  left: 13px;
  bottom: 22px;
  font-size: 13px;
}
.impact-list {
  display: grid;
  gap: 22px;
  margin: 30px 0;
}
.impact-list > div {
  display: flex;
  align-items: center;
  gap: 14px;
}
.impact-icon {
  width: 42px;
  height: 42px;
  border: 1px solid #34405b;
  border-radius: 13px;
  display: grid;
  place-items: center;
  color: var(--accent);
  background: #1b2233;
}
.impact-list h3 {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 5px;
}
.impact-list p {
  font-size: 12px;
  color: var(--muted);
}
.personal-note {
  border-top: 1px solid #30384a;
  padding-top: 22px;
  font-size: 13px;
  color: var(--muted);
  line-height: 2;
}
.personal-note span {
  display: block;
  color: var(--ink);
  margin-top: 5px;
}
.donation-form {
  background: var(--surface);
  border: 1px solid #394257;
  border-radius: 24px;
  padding: 30px;
  box-shadow: 0 24px 64px #00000026;
  animation: reveal 650ms 100ms cubic-bezier(0.2, 0, 0, 1) both;
}
.form-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 30px;
}
.form-heading h3 {
  font-size: 20px;
  font-weight: 700;
  margin-top: 9px;
}
.form-heart {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: #b7c4ff10;
  display: grid;
  place-items: center;
  color: var(--accent);
}
fieldset {
  min-width: 0;
  border: 0;
  padding: 0;
  margin: 0;
}
legend {
  font-size: 14px;
  font-weight: 600;
  margin-bottom: 14px;
}
.amount-options {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 10px;
  margin-bottom: 22px;
}
.amount-option {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
  background: #121724;
  border: 1px solid var(--line);
  border-radius: 13px;
  padding: 25px 6px 14px;
  transition:
    background 180ms,
    border-color 180ms,
    transform 180ms cubic-bezier(0.2, 0, 0, 1);
}
.amount-option strong {
  font-size: 18px;
  font-weight: 700;
}
.currency {
  font-size: 11px;
  color: var(--muted);
}
.amount-option.selected {
  background: #293452;
  border-color: var(--accent);
  box-shadow: inset 0 0 0 1px var(--accent);
  color: #e0e6ff;
}
.option-indicator {
  position: absolute;
  top: 8px;
  right: 9px;
  width: 13px;
  height: 13px;
  border: 1px solid #77849e;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 8px;
}
.selected .option-indicator {
  background: var(--accent);
  border-color: var(--accent);
  color: #15203e;
}
.field-label {
  display: block;
  font-size: 12px;
  font-weight: 500;
  margin-bottom: 9px;
  color: #d4daea;
}
input,
textarea {
  width: 100%;
  min-width: 0;
  background: #111622;
  border: 1px solid var(--line);
  border-radius: 10px;
  color: var(--ink);
  padding: 12px 13px;
  font: inherit;
  font-size: 13px;
  transition:
    border-color 180ms,
    box-shadow 180ms;
}
input::placeholder,
textarea::placeholder {
  color: #98a4bb;
  opacity: 1;
}
textarea {
  resize: vertical;
  min-height: 82px;
  display: block;
  line-height: 1.8;
}
input:focus,
textarea:focus {
  border-color: var(--accent);
  box-shadow: 0 0 0 3px #b7c4ff1a;
  outline: none;
}
.amount-input-wrap {
  position: relative;
}
.amount-input-wrap input {
  direction: ltr;
  text-align: right;
  padding-inline: 70px 14px;
  font-size: 17px;
  font-weight: 600;
  appearance: textfield;
}
.amount-input-wrap input::-webkit-inner-spin-button {
  appearance: none;
}
.amount-input-wrap > span {
  position: absolute;
  left: 15px;
  top: 15px;
  color: var(--muted);
  font-size: 12px;
  pointer-events: none;
}
.field-help {
  font-size: 11px;
  color: var(--muted);
  margin-top: 9px;
}
.field-error {
  color: #ffc0c7;
  font-size: 12px;
  line-height: 1.8;
}
.field-error:not(:empty) {
  margin-top: 8px;
}
.details-heading {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-top: 1px solid #353e51;
  padding-top: 23px;
  margin: 24px 0 18px;
  font-size: 13px;
}
.details-heading span:last-child {
  color: var(--muted);
  font-size: 11px;
}
.identity-fields {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
.message-label {
  margin-top: 17px;
}
.payment-button {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 11px;
  width: 100%;
  background: #b7c4ff;
  color: #141d38;
  border: 1px solid transparent;
  border-radius: 12px;
  padding: 16px 12px;
  margin-top: 24px;
  font-size: 14px;
  font-weight: 800;
  box-shadow: 0 5px 18px #070c1a33;
  transition:
    background 180ms,
    transform 180ms cubic-bezier(0.2, 0, 0, 1),
    box-shadow 180ms;
}
.payment-arrow {
  margin-right: auto;
  transition: transform 180ms;
}
.payment-button > svg:first-child {
  margin-left: auto;
}
.payment-button:disabled {
  background: #343d52;
  color: #bbc4d8;
  cursor: not-allowed;
  box-shadow: none;
}
.payment-note {
  display: flex;
  justify-content: center;
  gap: 7px;
  align-items: center;
  color: var(--muted);
  font-size: 11px;
  margin-top: 14px;
}
.payment-error,
.payment-success {
  padding: 12px;
  border: 1px solid #81505e;
  background: #34212b;
  border-radius: 10px;
  color: #ffd3d9;
  font-size: 13px;
  line-height: 1.8;
  margin-top: 18px;
}
.payment-success {
  background: #1c342d;
  border-color: #4e806e;
  color: #c5f2df;
}
.supporters-section {
  margin-top: 80px;
  border-top: 1px solid #30384a;
  padding-top: 34px;
}
.supporters-heading {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 20px;
  margin-bottom: 26px;
}
.supporters-heading h3 {
  font-size: 23px;
  font-weight: 700;
  margin-top: 9px;
}
.supporters-heading h3 span {
  color: var(--accent);
  font-size: 18px;
}
.supporters-heading > p {
  color: var(--muted);
  font-size: 13px;
}
.supporters-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 14px;
}
.supporter-card {
  background: #171c28;
  border: 1px solid #364056;
  border-radius: 15px;
  padding: 21px;
  transition:
    border-color 200ms,
    transform 200ms cubic-bezier(0.2, 0, 0, 1);
}
.supporter-top {
  display: flex;
  gap: 11px;
  align-items: center;
}
.supporter-avatar {
  width: 39px;
  height: 39px;
  flex-shrink: 0;
  display: grid;
  place-items: center;
  border-radius: 12px;
  background: #2b3551;
  color: #d3dcff;
}
.supporter-person {
  flex: 1;
  min-width: 0;
}
.supporter-person h4 {
  font-size: 14px;
  font-weight: 600;
  overflow-wrap: anywhere;
}
.supporter-person p {
  font-size: 11px;
  color: var(--muted);
  margin-top: 5px;
}
.supporter-amount {
  color: var(--accent);
  font-size: 13px;
  flex-shrink: 0;
}
.supporter-message {
  border-top: 1px solid #303a50;
  padding-top: 14px;
  margin-top: 15px;
  color: #c7cede;
  font-size: 13px;
  line-height: 2;
  overflow-wrap: anywhere;
}
.supporters-state {
  text-align: center;
  border: 1px dashed #46516a;
  border-radius: 16px;
  padding: 32px 18px;
  color: var(--muted);
  font-size: 14px;
}
.supporters-state > svg {
  color: var(--accent);
  margin-bottom: 12px;
}
.supporters-state > span {
  display: block;
  font-size: 12px;
  margin-top: 8px;
}
.secondary-button {
  padding: 11px 20px;
  border: 1px solid #65728f;
  border-radius: 10px;
  font-size: 13px;
  color: #d3ddff;
  background: #20283a;
  transition: background 180ms;
}
.supporters-state button {
  margin-top: 16px;
}
.load-more {
  text-align: center;
  margin-top: 24px;
}
.secondary-button:disabled {
  cursor: wait;
  color: var(--muted);
}
.spinner {
  animation: spin 1s linear infinite;
}
button:focus-visible {
  outline: 3px solid #d9e1ff;
  outline-offset: 4px;
}
button:active:not(:disabled) {
  transform: scale(0.98);
}
.notice-enter-active,
.notice-leave-active {
  transition:
    opacity 200ms,
    transform 200ms;
}
.notice-enter-from,
.notice-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
@media (hover: hover) {
  .amount-option:hover:not(:disabled) {
    border-color: var(--accent);
    transform: translateY(-2px);
  }
  .payment-button:hover:not(:disabled) {
    background: #ced7ff;
    box-shadow: 0 8px 24px #050a184d;
    transform: translateY(-2px);
  }
  .payment-button:hover .payment-arrow {
    transform: translateX(-3px);
  }
  .supporter-card:hover {
    border-color: #7483ab;
    transform: translateY(-2px);
  }
  .secondary-button:hover:not(:disabled) {
    background: #2d3851;
  }
}
@keyframes reveal {
  from {
    opacity: 0;
    transform: translateY(18px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
@keyframes heart-arrive {
  from {
    opacity: 0;
    transform: translateY(14px) rotate(-16deg);
  }
  to {
    opacity: 1;
    transform: translateY(0) rotate(-9deg);
  }
}
@keyframes orbit-reveal {
  from {
    opacity: 0;
    transform: scale(0.85) rotate(-25deg);
  }
  to {
    opacity: 1;
    transform: scale(1) rotate(0);
  }
}
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
@media (max-width: 960px) {
  .donation-layout {
    gap: 36px;
  }
  .donation-form {
    padding: 24px;
  }
  .donation-intro h2 {
    font-size: 31px;
  }
  .amount-option strong {
    font-size: 16px;
  }
}
@media (max-width: 720px) {
  .donation-section {
    padding: 48px 18px;
  }
  .donation-layout {
    grid-template-columns: 1fr;
    gap: 30px;
  }
  .donation-intro {
    text-align: center;
  }
  .donation-intro h2 {
    font-size: 32px;
    line-height: 1.65;
  }
  .heart-art {
    margin: 15px auto 2px;
    width: 140px;
    height: 125px;
  }
  .heart-orbit {
    width: 120px;
    height: 120px;
  }
  .orbit-two {
    width: 96px;
    height: 96px;
  }
  .heart-tile {
    width: 68px;
    height: 68px;
    border-radius: 22px;
    font-size: 27px;
  }
  .intro-copy {
    margin: auto;
    font-size: 14px;
  }
  .impact-list {
    display: none;
  }
  .personal-note {
    border: 0;
    padding: 0;
    margin-top: 16px;
  }
  .personal-note span {
    display: inline;
    margin-right: 8px;
  }
  .donation-form {
    text-align: right;
    padding: 24px 20px;
    border-radius: 20px;
  }
  .supporters-section {
    margin-top: 48px;
    padding-top: 28px;
  }
  .supporters-heading {
    display: block;
  }
  .supporters-heading h3 {
    font-size: 20px;
  }
  .supporters-heading > p {
    margin-top: 12px;
  }
  .supporters-grid {
    grid-template-columns: 1fr;
  }
  .supporter-card {
    padding: 18px;
  }
  .form-heading h3 {
    font-size: 19px;
  }
}
@media (max-width: 380px) {
  .donation-section {
    padding-inline: 12px;
  }
  .donation-form {
    padding: 22px 16px;
  }
  .identity-fields {
    grid-template-columns: 1fr;
  }
  .amount-options {
    gap: 7px;
  }
  .amount-option strong {
    font-size: 15px;
  }
  .donation-intro h2 {
    font-size: 29px;
  }
  .supporter-top {
    flex-wrap: wrap;
  }
  .supporter-amount {
    margin-right: 50px;
  }
  .form-heading h3 {
    font-size: 17px;
  }
}
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation: none !important;
    transition: none !important;
    scroll-behavior: auto !important;
  }
  .amount-option:hover,
  .payment-button:hover,
  .supporter-card:hover,
  button:active:not(:disabled) {
    transform: none !important;
  }
}
</style>
