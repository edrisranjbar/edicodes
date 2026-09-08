<template>
  <main class="donations-admin" dir="rtl" :aria-busy="loading">
    <section class="records-panel">
      <div class="panel-heading">
        <div>
          <h2>تراکنش‌ها</h2>
          <p>جدیدترین حمایت‌ها در ابتدای فهرست نمایش داده می‌شوند.</p>
        </div>
        <span class="count">{{ loading || error ? '—' : number(pagination.total) }} نتیجه</span>
      </div>
      <form class="filters" @submit.prevent="applyFilters">
        <div>
          <label for="donation-status">وضعیت پرداخت</label
          ><select id="donation-status" v-model="filters.status">
            <option value="all">همه وضعیت‌ها</option>
            <option value="paid">پرداخت موفق</option>
            <option value="pending">در انتظار پرداخت</option>
            <option value="failed">پرداخت ناموفق</option>
          </select>
        </div>
        <div>
          <label for="donation-from">تاریخ شروع</label
          ><input
            id="donation-from"
            v-model="filters.start_date"
            type="text"
            dir="ltr"
            placeholder="۱۴۰۵/۰۶/۱۷"
            maxlength="10"
            aria-describedby="jalali-help date-error"
            :aria-invalid="!!dateError"
          />
        </div>
        <div>
          <label for="donation-to">تاریخ پایان</label
          ><input
            id="donation-to"
            v-model="filters.end_date"
            type="text"
            dir="ltr"
            placeholder="۱۴۰۵/۰۶/۱۷"
            maxlength="10"
            aria-describedby="jalali-help date-error"
            :aria-invalid="!!dateError"
          />
        </div>
        <div>
          <label for="donation-limit">تعداد در صفحه</label
          ><select id="donation-limit" v-model="filters.per_page">
            <option :value="10">۱۰</option>
            <option :value="25">۲۵</option>
            <option :value="50">۵۰</option>
          </select>
        </div>
        <button class="button primary" :disabled="loading || updating !== null">اعمال فیلتر</button
        ><button
          type="button"
          class="button"
          :disabled="loading || updating !== null"
          @click="resetFilters"
        >
          پاک کردن
        </button>
      </form>
      <p id="jalali-help" class="date-help">تاریخ شمسی با قالب سال/ماه/روز؛ مانند ۱۴۰۵/۰۶/۱۷</p>
      <p v-if="dateError" id="date-error" class="feedback danger" role="alert">{{ dateError }}</p>
      <p
        v-if="feedback"
        class="feedback"
        :class="{ danger: feedbackError }"
        :role="feedbackError ? 'alert' : 'status'"
      >
        {{ feedback }}
      </p>
      <div v-if="loading" class="state" role="status">در حال دریافت حمایت‌ها…</div>
      <div v-else-if="error" class="state danger" role="alert">
        <p>{{ error }}</p>
        <button class="button" @click="fetchDonations">تلاش دوباره</button>
      </div>
      <div v-else-if="!donations.length" class="state">
        <span class="empty-heart" aria-hidden="true">♡</span>
        <h2>حمایتی پیدا نشد</h2>
        <p>برای مشاهده نتایج دیگر، وضعیت یا بازه تاریخ را تغییر دهید.</p>
      </div>
      <div v-else class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>حامی</th>
              <th>مبلغ حمایت</th>
              <th>وضعیت پرداخت</th>
              <th>نمایش عمومی</th>
              <th>تاریخ ثبت</th>
              <th>جزئیات</th>
            </tr>
          </thead>
          <tbody>
            <template v-for="donation in donations" :key="donation.id">
              <tr :class="{ expanded: expanded === donation.id }">
                <td>
                  <div class="donor">
                    <span class="avatar" aria-hidden="true">{{
                      Array.from(donation.name || 'ناشناس')[0]
                    }}</span>
                    <div>
                      <strong>{{ donation.name || 'ناشناس' }}</strong
                      ><span class="email" dir="ltr">{{ donation.email || 'ایمیل ثبت نشده' }}</span>
                    </div>
                  </div>
                </td>
                <td class="amount">
                  {{ donation.formatted_amount || '—'
                  }}<small>شناسه {{ number(donation.id) }}</small>
                </td>
                <td>
                  <span class="badge" :class="donation.status"
                    ><span aria-hidden="true">{{
                      donation.status === 'paid' ? '✓' : donation.status === 'failed' ? '×' : '◷'
                    }}</span>
                    {{ statusLabel(donation) }}</span
                  >
                </td>
                <td>
                  <button
                    class="visibility"
                    :class="{ public: donation.is_public }"
                    :disabled="updating !== null"
                    :aria-pressed="!!donation.is_public"
                    :aria-label="`${donation.is_public ? 'خصوصی کردن' : 'عمومی کردن'} حمایت ${donation.name || number(donation.id)}`"
                    @click="toggleVisibility(donation)"
                  >
                    <font-awesome-icon :icon="donation.is_public ? 'eye' : 'lock'" />
                    {{
                      updating === donation.id
                        ? 'در حال ذخیره…'
                        : donation.is_public
                          ? 'عمومی'
                          : 'خصوصی'
                    }}
                  </button>
                </td>
                <td>{{ formatDate(donation.created_at) }}</td>
                <td>
                  <button
                    class="details-button"
                    :aria-expanded="expanded === donation.id"
                    :aria-controls="`donation-detail-${donation.id}`"
                    @click="expanded = expanded === donation.id ? null : donation.id"
                  >
                    {{ expanded === donation.id ? 'بستن' : 'مشاهده' }}
                    <font-awesome-icon icon="chevron-down" />
                  </button>
                </td>
              </tr>
              <tr
                v-if="expanded === donation.id"
                :id="`donation-detail-${donation.id}`"
                class="detail-row"
              >
                <td colspan="6">
                  <div class="detail-content">
                    <div>
                      <h3>پیام حامی</h3>
                      <p class="message">
                        {{ donation.message || 'پیامی برای این حمایت ثبت نشده است.' }}
                      </p>
                    </div>
                    <div>
                      <h3>اطلاعات پرداخت</h3>
                      <dl>
                        <div>
                          <dt>کد پیگیری</dt>
                          <dd dir="ltr">
                            {{ donation.ref_id || '—' }}
                            <button
                              v-if="donation.ref_id"
                              class="copy-button"
                              @click="copyReference(donation.ref_id)"
                            >
                              کپی
                            </button>
                          </dd>
                        </div>
                        <div>
                          <dt>روش پرداخت</dt>
                          <dd>{{ donation.payment_method || '—' }}</dd>
                        </div>
                        <div>
                          <dt>آخرین تغییر</dt>
                          <dd>{{ formatDate(donation.updated_at) }}</dd>
                        </div>
                      </dl>
                    </div>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
      <footer v-if="!loading && !error && donations.length" class="pagination">
        <span
          >{{ number((pagination.current_page - 1) * pagination.per_page + 1) }} تا
          {{ number(Math.min(pagination.current_page * pagination.per_page, pagination.total)) }} از
          {{ number(pagination.total) }} حمایت</span
        >
        <div>
          <button
            class="button"
            :disabled="pagination.current_page <= 1 || updating !== null"
            @click="changePage(pagination.current_page - 1)"
          >
            قبلی</button
          ><span
            >صفحه {{ number(pagination.current_page) }} از {{ number(pagination.last_page) }}</span
          ><button
            class="button"
            :disabled="pagination.current_page >= pagination.last_page || updating !== null"
            @click="changePage(pagination.current_page + 1)"
          >
            بعدی
          </button>
        </div>
      </footer>
    </section>
    <p class="privacy-note">
      <font-awesome-icon icon="lock" /> نمایش عمومی مستقل از وضعیت پرداخت است. حمایت‌های موفق و
      عمومی می‌توانند در فهرست حامیان سایت نمایش داده شوند.
    </p>
  </main>
</template>
<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import moment from 'moment-jalaali'
import { API_URL } from '@/config'
defineOptions({ name: 'AdminDonations' })
const loading = ref(true)
const updating = ref(null)
const donations = ref([])
const expanded = ref(null)
const error = ref('')
const dateError = ref('')
const feedback = ref('')
const feedbackError = ref(false)
const defaults = () => ({ status: 'all', start_date: '', end_date: '', per_page: 10 })
const filters = reactive(defaults())
let applied = defaults()
const pagination = reactive({ current_page: 1, last_page: 1, per_page: 10, total: 0 })
const headers = () => ({ Authorization: `Bearer ${localStorage.getItem('admin_token')}` })
const number = (value) => new Intl.NumberFormat('fa-IR').format(Number(value) || 0)
const formatDate = (value) =>
  value && !Number.isNaN(new Date(value).getTime())
    ? new Intl.DateTimeFormat('fa-IR', { dateStyle: 'medium', timeStyle: 'short' }).format(
        new Date(value),
      )
    : '—'
const statusLabel = (donation) =>
  ({ paid: 'پرداخت موفق', pending: 'در انتظار پرداخت', failed: 'پرداخت ناموفق' })[
    donation.status
  ] ||
  donation.status_in_persian ||
  'نامشخص'
let controller
let requestId = 0
const fetchDonations = async () => {
  controller?.abort()
  controller = new AbortController()
  const id = ++requestId
  loading.value = true
  error.value = ''
  expanded.value = null
  try {
    const response = await axios.get(`${API_URL}/admin/donations`, {
      headers: headers(),
      signal: controller.signal,
      params: { ...applied, page: pagination.current_page, sort: '-created_at' },
    })
    if (id !== requestId) return
    const body = response.data || {}
    if (body.success === false) throw new Error('Request failed')
    const data = body.data && !Array.isArray(body.data) && 'data' in body.data ? body.data : body
    donations.value = (Array.isArray(data.data) ? data.data : []).filter(Boolean)
    Object.assign(pagination, {
      current_page: Number(data.current_page) || 1,
      last_page: Number(data.last_page) || 1,
      per_page: Number(data.per_page) || Number(applied.per_page),
      total: Number(data.total) || donations.value.length,
    })
  } catch (err) {
    if (id === requestId && !axios.isCancel(err))
      error.value = 'دریافت حمایت‌ها ممکن نشد. دوباره تلاش کنید.'
  } finally {
    if (id === requestId) loading.value = false
  }
}
const applyFilters = () => {
  dateError.value = ''
  const parseDate = (value) => {
    if (!value.trim()) return ''
    const normalized = value
      .trim()
      .replace(/[۰-۹]/g, (digit) => '۰۱۲۳۴۵۶۷۸۹'.indexOf(digit))
      .replace(/[٠-٩]/g, (digit) => '٠١٢٣٤٥٦٧٨٩'.indexOf(digit))
    const parsed = moment(normalized, 'jYYYY/jMM/jDD', 'en', true)
    if (
      !/^\d{4}\/\d{2}\/\d{2}$/.test(normalized) ||
      !parsed.isValid() ||
      parsed.format('jYYYY/jMM/jDD') !== normalized
    )
      throw new Error('invalid date')
    return parsed.format('YYYY-MM-DD')
  }
  try {
    const start_date = parseDate(filters.start_date)
    const end_date = parseDate(filters.end_date)
    if (start_date && end_date && start_date > end_date) {
      dateError.value = 'تاریخ پایان باید برابر یا بعد از تاریخ شروع باشد.'
      return
    }
    applied = { ...filters, start_date, end_date }
  } catch {
    dateError.value = 'تاریخ شمسی معتبر با قالب ۱۴۰۵/۰۶/۱۷ وارد کنید.'
    return
  }
  pagination.current_page = 1
  fetchDonations()
}
const resetFilters = () => {
  Object.assign(filters, defaults())
  applyFilters()
}
const changePage = (page) => {
  if (loading.value || updating.value !== null || page < 1 || page > pagination.last_page) return
  pagination.current_page = page
  fetchDonations()
}
const toggleVisibility = async (donation) => {
  if (updating.value !== null) return
  updating.value = donation.id
  feedback.value = ''
  feedbackError.value = false
  const next = !donation.is_public
  try {
    const response = await axios.put(
      `${API_URL}/admin/donations/${donation.id}`,
      { is_public: next },
      { headers: headers() },
    )
    if (response.data?.success === false) throw new Error('Update failed')
    donation.is_public = response.data?.data?.is_public ?? next
    feedback.value = `نمایش حمایت ${donation.name || number(donation.id)} به ${donation.is_public ? 'عمومی' : 'خصوصی'} تغییر کرد.`
  } catch {
    feedbackError.value = true
    feedback.value = 'تغییر نمایش ذخیره نشد. وضعیت قبلی حفظ شد؛ دوباره تلاش کنید.'
  } finally {
    updating.value = null
  }
}
const copyReference = async (value) => {
  try {
    await navigator.clipboard.writeText(String(value))
    feedbackError.value = false
    feedback.value = 'کد پیگیری کپی شد.'
  } catch {
    feedbackError.value = true
    feedback.value = 'کپی ممکن نشد. کد پیگیری را انتخاب و دستی کپی کنید.'
  }
}
onMounted(fetchDonations)
onBeforeUnmount(() => controller?.abort())
</script>
<style scoped>
.date-help {
  margin: 12px 24px;
  color: #bdc8dd;
  font-size: 12px;
}
.donations-admin {
  color: #f4f6fc;
  max-width: 1440px;
  margin: auto;
}
.page-heading,
.panel-heading,
.pagination {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 20px;
}
.page-heading {
  margin-bottom: 28px;
}
h1 {
  font-size: 28px;
  font-weight: 800;
  margin: 6px 0 10px;
}
h2 {
  font-size: 18px;
  font-weight: 700;
}
.page-heading p,
.panel-heading p,
.privacy-note {
  font-size: 13px;
  color: #bdc8dd;
  line-height: 1.9;
}
.page-heading .eyebrow {
  color: #bbc9ff;
  font-size: 12px;
}
.records-panel {
  background: #161d2b;
  border: 1px solid #3a465e;
  border-radius: 18px;
  overflow: hidden;
}
.panel-heading {
  padding: 24px;
}
.panel-heading p {
  margin-top: 6px;
}
.count {
  color: #c6d2ff;
  background: #b7c4ff12;
  padding: 8px 12px;
  border-radius: 9px;
  font-size: 12px;
  white-space: nowrap;
}
.filters {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  align-items: flex-end;
  background: #121927;
  padding: 20px 24px;
  border-block: 1px solid #354159;
}
.filters > div {
  flex: 1;
  min-width: 145px;
}
label {
  display: block;
  font-size: 12px;
  color: #cbd5e8;
  margin-bottom: 8px;
}
select,
input {
  display: block;
  width: 100%;
  background: #1c2536;
  border: 1px solid #65738e;
  border-radius: 9px;
  color: #f2f5fc;
  padding: 10px 12px;
  font: inherit;
  font-size: 13px;
  min-height: 42px;
  color-scheme: dark;
}
.button {
  background: #26324a;
  border: 1px solid #63728f;
  border-radius: 9px;
  padding: 10px 15px;
  font-size: 12px;
  color: #e0e8fc;
  min-height: 42px;
}
.button.primary {
  background: #b7c4ff;
  color: #18243f;
  border-color: #b7c4ff;
  font-weight: 700;
}
button:disabled {
  opacity: 0.55;
  cursor: not-allowed;
}
button:focus-visible,
input:focus-visible,
select:focus-visible {
  outline: 3px solid #c5d1ff;
  outline-offset: 3px;
}
.table-scroll {
  overflow-x: auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  text-align: right;
  white-space: nowrap;
}
th {
  background: #192233;
  color: #bdc9df;
  font-size: 12px;
  font-weight: 500;
  padding: 16px 20px;
}
td {
  padding: 20px;
  border-top: 1px solid #303c53;
  font-size: 13px;
}
tbody tr:hover,
tr.expanded {
  background: #1b2639;
}
.donor {
  display: flex;
  align-items: center;
  gap: 12px;
}
.avatar {
  display: grid;
  place-items: center;
  width: 40px;
  height: 40px;
  background: #2d3a57;
  color: #cedaff;
  border-radius: 12px;
  flex-shrink: 0;
}
.donor strong {
  display: block;
  max-width: 220px;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 14px;
}
.email {
  display: block;
  margin-top: 6px;
  font-size: 11px;
  color: #b5c2d9;
}
.amount {
  font-weight: 700;
  color: #e0e7fc;
}
.amount small {
  display: block;
  font-weight: 400;
  font-size: 11px;
  color: #b5c2d9;
  margin-top: 7px;
}
.badge {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: #2b3547;
  color: #d8e0ef;
  border: 1px solid #59647a;
  border-radius: 8px;
  padding: 6px 9px;
  font-size: 11px;
}
.badge.paid {
  background: #18382f;
  border-color: #3d7762;
  color: #9bf1cf;
}
.badge.pending {
  background: #39321d;
  border-color: #807047;
  color: #f4d889;
}
.badge.failed {
  background: #3c2532;
  border-color: #8b5064;
  color: #ffc1d0;
}
.visibility {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: #cbd5e7;
  border: 1px solid #65738e;
  background: #222d41;
  border-radius: 8px;
  padding: 8px 10px;
  font-size: 12px;
}
.visibility.public {
  color: #a9edcf;
  border-color: #548d73;
  background: #1a352d;
}
.details-button {
  color: #c1d0ff;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px;
  border-radius: 6px;
  font-size: 12px;
}
.details-button[aria-expanded='true'] svg {
  transform: rotate(180deg);
}
.detail-row {
  background: #121b2b !important;
}
.detail-content {
  display: grid;
  grid-template-columns: 1.2fr 1fr;
  gap: 40px;
  padding: 4px 12px 10px;
  white-space: normal;
}
.detail-content h3 {
  font-size: 13px;
  color: #c5d2f2;
  margin-bottom: 15px;
}
.message {
  white-space: pre-wrap;
  overflow-wrap: anywhere;
  line-height: 2;
  color: #d1dbee;
  font-size: 13px;
}
.detail-content dl > div {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 12px;
  font-size: 12px;
}
.detail-content dt {
  color: #aebdd6;
}
.detail-content dd {
  overflow-wrap: anywhere;
}
.copy-button {
  color: #c4d2ff;
  text-decoration: underline;
  margin-inline: 8px;
}
.pagination {
  padding: 20px 24px;
  border-top: 1px solid #354159;
  color: #bcc9de;
  font-size: 12px;
}
.pagination > div {
  display: flex;
  align-items: center;
  gap: 15px;
}
.privacy-note {
  margin-top: 20px;
}
.privacy-note svg {
  margin-left: 6px;
  color: #b7c4ff;
}
.feedback {
  margin: 16px 24px;
  padding: 12px 16px;
  border: 1px solid #548d73;
  background: #1a352d;
  color: #baf3d9;
  border-radius: 9px;
  font-size: 13px;
}
.danger {
  color: #ffc4d0 !important;
}
.feedback.danger {
  background: #3c2532;
  border-color: #8b5064;
}
.state {
  text-align: center;
  padding: 60px 24px;
  color: #c3cfe3;
  font-size: 14px;
}
.state p {
  margin: 12px 0;
  line-height: 1.9;
}
.empty-heart {
  display: block;
  font-size: 36px;
  color: #bdccff;
  margin-bottom: 16px;
}
@media (max-width: 700px) {
  .page-heading,
  .panel-heading,
  .pagination {
    align-items: flex-start;
    flex-direction: column;
  }
  .page-heading h1 {
    font-size: 25px;
  }
  .panel-heading,
  .filters,
  .pagination {
    padding: 18px;
  }
  .filters > div {
    min-width: 130px;
  }
  .detail-content {
    grid-template-columns: 1fr;
    gap: 24px;
  }
  .pagination > div {
    gap: 10px;
  }
  .privacy-note {
    font-size: 12px;
  }
}
</style>
