<template>
  <AppLayout title="রিপোর্টার বিলিং ও সম্মানী">
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">মডিউল</span>
            <h2 class="text-2xl font-bold text-gray-800">রিপোর্টার বিলিং ও সম্মানী পেরোল</h2>
          </div>
          <p class="text-sm text-gray-500 mt-1">প্রতিনিধিদের প্রকাশিত খবরের হিসাব অনুযায়ী স্বয়ংক্রিয় বিল তৈরি ও পেমেন্ট রেকর্ড</p>
        </div>
        <div class="flex items-center gap-3">
          <button 
            @click="openCreateModal"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            নতুন বিল তৈরি করুন
          </button>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">মোট পরিশোধিত</span>
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">৳</div>
          </div>
          <p class="text-2xl font-bold text-gray-800 mt-2">৳{{ Number(stats.total_paid).toLocaleString('bn-BD') }}</p>
          <span class="text-xs text-emerald-600 font-medium">সফল পেমেন্ট সমূহ</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">বকেয়া বিল (Pending)</span>
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">৳</div>
          </div>
          <p class="text-2xl font-bold text-amber-600 mt-2">৳{{ Number(stats.total_pending).toLocaleString('bn-BD') }}</p>
          <span class="text-xs text-amber-600 font-medium">অনুমোদনের অপেক্ষায়</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">হিসাবকৃত মোট নিউজ</span>
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">📄</div>
          </div>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ stats.total_articles }} টি</p>
          <span class="text-xs text-blue-600 font-medium">রিপোর্টারদের প্রকাশিত খবর</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold text-gray-400 uppercase">মোট ভাউচার সংখ্যা</span>
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">🧾</div>
          </div>
          <p class="text-2xl font-bold text-gray-800 mt-2">{{ stats.total_bills }} টি</p>
          <span class="text-xs text-purple-600 font-medium">বিল হিস্ট্রি</span>
        </div>
      </div>

      <!-- Filters & Search -->
      <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-3">
          <div>
            <label class="text-xs font-medium text-gray-500 block mb-1">বিলিং মাস:</label>
            <input 
              v-model="filterMonth" 
              type="month" 
              class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 focus:ring-emerald-500 focus:border-emerald-500"
              @change="applyFilter"
            />
          </div>
          <div>
            <label class="text-xs font-medium text-gray-500 block mb-1">স্ট্যাটাস ফিল্টার:</label>
            <select 
              v-model="filterStatus" 
              class="text-sm border border-gray-200 rounded-xl px-3 py-1.5 focus:ring-emerald-500 focus:border-emerald-500"
              @change="applyFilter"
            >
              <option value="">সকল স্ট্যাটাস</option>
              <option value="pending">পেন্ডিং (Pending)</option>
              <option value="approved">অনুমোদিত (Approved)</option>
              <option value="paid">পরিশোধিত (Paid)</option>
            </select>
          </div>
        </div>

        <button 
          v-if="filterMonth || filterStatus"
          @click="resetFilter"
          class="text-xs text-red-600 hover:underline">
          ফিল্টার মুছুন
        </button>
      </div>

      <!-- Bills Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50/75 border-b border-gray-100 text-xs text-gray-400 font-semibold uppercase">
              <tr>
                <th class="px-5 py-3.5">ভাউচার নং</th>
                <th class="px-5 py-3.5">রিপোর্টার</th>
                <th class="px-5 py-3.5">বিলিং মাস</th>
                <th class="px-5 py-3.5 text-center">মোট খবর</th>
                <th class="px-5 py-3.5">রেট</th>
                <th class="px-5 py-3.5 font-bold text-gray-700">মোট প্রদেয়</th>
                <th class="px-5 py-3.5">পদ্ধতি</th>
                <th class="px-5 py-3.5">স্ট্যাটাস</th>
                <th class="px-5 py-3.5 text-right">অ্যাকশন</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="bill in bills.data" :key="bill.id" class="hover:bg-gray-50/50 transition">
                <td class="px-5 py-4 font-mono font-medium text-gray-700">#BILL-{{ String(bill.id).padStart(4, '0') }}</td>
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xs">
                      {{ bill.reporter?.name?.charAt(0) || 'R' }}
                    </div>
                    <div>
                      <span class="font-semibold text-gray-800 block">{{ bill.reporter?.name || 'অজ্ঞাত' }}</span>
                      <span class="text-xs text-gray-400">{{ bill.reporter?.reporter_type || 'প্রতিনিধি' }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4 font-medium text-gray-700">{{ bill.billing_month }}</td>
                <td class="px-5 py-4 text-center font-bold text-gray-800">{{ bill.total_articles }} টি</td>
                <td class="px-5 py-4 text-xs text-gray-500">৳{{ bill.rate_per_article }}/খবর</td>
                <td class="px-5 py-4 font-bold text-emerald-600">৳{{ Number(bill.total_payable).toLocaleString('bn-BD') }}</td>
                <td class="px-5 py-4 text-xs capitalize text-gray-600">
                  <span class="font-medium">{{ bill.payment_method }}</span>
                  <span v-if="bill.account_number" class="block text-gray-400 text-[11px]">{{ bill.account_number }}</span>
                </td>
                <td class="px-5 py-4">
                  <span :class="[
                    'px-2.5 py-1 rounded-full text-xs font-semibold',
                    bill.status === 'paid' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
                    bill.status === 'approved' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                    'bg-amber-50 text-amber-700 border border-amber-200'
                  ]">
                    {{ bill.status === 'paid' ? 'পরিশোধিত' : bill.status === 'approved' ? 'অনুমোদিত' : 'পেন্ডিং' }}
                  </span>
                </td>
                <td class="px-5 py-4 text-right space-x-2">
                  <button 
                    @click="openVoucherModal(bill)"
                    class="p-1.5 text-gray-400 hover:text-emerald-600 rounded-lg hover:bg-emerald-50 transition" 
                    title="রসিদ / ভাউচার দেখুন">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                  </button>
                  <button 
                    v-if="bill.status !== 'paid'"
                    @click="openStatusModal(bill)"
                    class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 transition" 
                    title="পেমেন্ট কনফার্ম করুন">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                  </button>
                  <button 
                    @click="deleteBill(bill.id)"
                    class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" 
                    title="মুছে ফেলুন">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>
                </td>
              </tr>
              <tr v-if="!bills.data || bills.data.length === 0">
                <td colspan="9" class="px-5 py-12 text-center text-gray-400 text-sm">
                  কোন বিল রেকর্ড পাওয়া যায়নি।
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create Bill Modal -->
      <div v-if="showCreateModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-5 shadow-xl">
          <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-800 text-lg">নতুন বিল তৈরি ও হিসাব</h3>
            <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
          </div>

          <form @submit.prevent="submitBill" class="space-y-4 text-sm">
            <div>
              <label class="block font-medium text-gray-700 mb-1">রিপোর্টার নির্বাচন করুন *</label>
              <select v-model="form.reporter_id" @change="autoCalculate" required class="w-full border border-gray-200 rounded-xl px-3 py-2">
                <option value="">নির্বাচন করুন</option>
                <option v-for="r in reporters" :key="r.id" :value="r.id">
                  {{ r.name }} ({{ r.email }})
                </option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">বিলিং মাস *</label>
                <input v-model="form.billing_month" @change="autoCalculate" type="month" required class="w-full border border-gray-200 rounded-xl px-3 py-2" />
              </div>
              <div>
                <label class="block font-medium text-gray-700 mb-1">প্রতি খবরের রেট (৳) *</label>
                <input v-model.number="form.rate_per_article" @input="recalc" type="number" min="0" required class="w-full border border-gray-200 rounded-xl px-3 py-2" />
              </div>
            </div>

            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-100 flex items-center justify-between">
              <div>
                <span class="text-xs text-emerald-800 font-semibold block">নিউজ সংখ্যা (স্বয়ংক্রিয় কাউন্ট):</span>
                <span class="text-lg font-bold text-emerald-900">{{ form.total_articles }} টি খবর</span>
              </div>
              <button type="button" @click="autoCalculate" class="text-xs text-emerald-700 font-semibold underline">
                {{ isCalculating ? 'গণনা হচ্ছে...' : 'পুনরায় হিসাব' }}
              </button>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">বোনাস / বিশেষ ভাতা (৳)</label>
                <input v-model.number="form.bonus_amount" @input="recalc" type="number" min="0" class="w-full border border-gray-200 rounded-xl px-3 py-2" placeholder="0" />
              </div>
              <div>
                <label class="block font-medium text-gray-700 mb-1">কর্তন / জরিমানা (৳)</label>
                <input v-model.number="form.deduction_amount" @input="recalc" type="number" min="0" class="w-full border border-gray-200 rounded-xl px-3 py-2" placeholder="0" />
              </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-xl flex items-center justify-between">
              <span class="font-bold text-gray-700">মোট প্রদেয় টাকা:</span>
              <span class="text-xl font-black text-emerald-600">৳{{ netPayable.toLocaleString('bn-BD') }}</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">পেমেন্ট মেথড *</label>
                <select v-model="form.payment_method" required class="w-full border border-gray-200 rounded-xl px-3 py-2">
                  <option value="bkash">বিকাশ (bKash)</option>
                  <option value="nagad">নগদ (Nagad)</option>
                  <option value="rocket">রকেট (Rocket)</option>
                  <option value="bank">ব্যাংক একাউন্ট</option>
                  <option value="cash">ক্যাশ / নগদ</option>
                </select>
              </div>
              <div>
                <label class="block font-medium text-gray-700 mb-1">হিসাব নম্বর / একাউন্ট</label>
                <input v-model="form.account_number" type="text" placeholder="017xxxxxxxx" class="w-full border border-gray-200 rounded-xl px-3 py-2" />
              </div>
            </div>

            <div>
              <label class="block font-medium text-gray-700 mb-1">নোট / মন্তব্য (ঐচ্ছিক)</label>
              <textarea v-model="form.notes" rows="2" class="w-full border border-gray-200 rounded-xl px-3 py-2" placeholder="বিশেষ কোনো শর্ত থাকলে লিখুন..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
              <button type="button" @click="showCreateModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50">বাতিল</button>
              <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold">বিল সংরক্ষণ করুন</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Update Status / Confirm Payment Modal -->
      <div v-if="showStatusModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-xl">
          <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-bold text-gray-800">পেমেন্ট ও স্ট্যাটাস আপডেট</h3>
            <button @click="showStatusModal = false" class="text-gray-400 hover:text-gray-600">✕</button>
          </div>

          <form @submit.prevent="submitStatusUpdate" class="space-y-4 text-sm">
            <div>
              <label class="block font-medium text-gray-700 mb-1">স্ট্যাটাস *</label>
              <select v-model="statusForm.status" class="w-full border border-gray-200 rounded-xl px-3 py-2">
                <option value="pending">পেন্ডিং (Pending)</option>
                <option value="approved">অনুমোদিত (Approved)</option>
                <option value="paid">পরিশোধিত (Paid)</option>
              </select>
            </div>

            <div v-if="statusForm.status === 'paid'" class="space-y-3">
              <div>
                <label class="block font-medium text-gray-700 mb-1">ট্রানজেকশন আইডি (TxID)</label>
                <input v-model="statusForm.transaction_id" type="text" placeholder="BKASH-XXXXXX" class="w-full border border-gray-200 rounded-xl px-3 py-2" />
              </div>
              <div>
                <label class="block font-medium text-gray-700 mb-1">পেমেন্ট মেথড</label>
                <select v-model="statusForm.payment_method" class="w-full border border-gray-200 rounded-xl px-3 py-2">
                  <option value="bkash">বিকাশ</option>
                  <option value="nagad">নগদ</option>
                  <option value="rocket">রকেট</option>
                  <option value="bank">ব্যাংক</option>
                  <option value="cash">ক্যাশ</option>
                </select>
              </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
              <button type="button" @click="showStatusModal = false" class="px-4 py-2 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-50">বাতিল</button>
              <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold">আপডেট করুন</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Voucher Print / View Modal -->
      <div v-if="showVoucherModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full p-8 space-y-6 shadow-2xl relative" id="printVoucherArea">
          <div class="flex items-center justify-between border-b pb-4">
            <div>
              <h2 class="text-xl font-black text-gray-900 tracking-tight">পেমেন্ট ভাউচার / মানি রসিদ</h2>
              <p class="text-xs text-gray-400 mt-0.5">ভাউচার নম্বর: #BILL-{{ String(activeVoucher?.id).padStart(4, '0') }}</p>
            </div>
            <div class="text-right">
              <span class="text-xs font-semibold px-3 py-1 rounded-full uppercase" :class="activeVoucher?.status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
                {{ activeVoucher?.status === 'paid' ? 'পরিশোধিত' : 'পেন্ডিং' }}
              </span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 text-xs">
            <div class="space-y-1">
              <p class="text-gray-400 uppercase font-semibold">প্রাপকের বিবরণ:</p>
              <p class="font-bold text-gray-800 text-sm">{{ activeVoucher?.reporter?.name }}</p>
              <p class="text-gray-500">{{ activeVoucher?.reporter?.email }}</p>
              <p class="text-gray-500">পদবী: {{ activeVoucher?.reporter?.reporter_type || 'প্রতিনিধি' }}</p>
            </div>
            <div class="space-y-1 text-right">
              <p class="text-gray-400 uppercase font-semibold">বিলিং সময়কাল:</p>
              <p class="font-bold text-gray-800 text-sm">{{ activeVoucher?.billing_month }}</p>
              <p class="text-gray-500">পদ্ধতি: {{ activeVoucher?.payment_method }}</p>
              <p v-if="activeVoucher?.account_number" class="text-gray-500">হিসাব: {{ activeVoucher?.account_number }}</p>
            </div>
          </div>

          <div class="border rounded-xl overflow-hidden text-xs">
            <table class="w-full text-left">
              <thead class="bg-gray-50 border-b text-gray-500">
                <tr>
                  <th class="p-3">বিবরণ</th>
                  <th class="p-3 text-center">পরিমাণ</th>
                  <th class="p-3 text-right">রেট</th>
                  <th class="p-3 text-right">মোট</th>
                </tr>
              </thead>
              <tbody class="divide-y text-gray-700">
                <tr>
                  <td class="p-3">প্রকাশিত সংবাদের সম্মানী</td>
                  <td class="p-3 text-center">{{ activeVoucher?.total_articles }} টি</td>
                  <td class="p-3 text-right">৳{{ activeVoucher?.rate_per_article }}</td>
                  <td class="p-3 text-right font-medium">৳{{ (activeVoucher?.total_articles * activeVoucher?.rate_per_article).toLocaleString('bn-BD') }}</td>
                </tr>
                <tr v-if="activeVoucher?.bonus_amount > 0">
                  <td class="p-3 text-emerald-600">বিশেষ পারফরম্যান্স ভাতা (+)</td>
                  <td class="p-3 text-center">-</td>
                  <td class="p-3 text-right">-</td>
                  <td class="p-3 text-right text-emerald-600 font-medium">+ ৳{{ activeVoucher?.bonus_amount }}</td>
                </tr>
                <tr v-if="activeVoucher?.deduction_amount > 0">
                  <td class="p-3 text-red-600">কর্তন / জরিমানা (-)</td>
                  <td class="p-3 text-center">-</td>
                  <td class="p-3 text-right">-</td>
                  <td class="p-3 text-right text-red-600 font-medium">- ৳{{ activeVoucher?.deduction_amount }}</td>
                </tr>
              </tbody>
              <tfoot class="bg-gray-50 font-bold border-t text-gray-800">
                <tr>
                  <td colspan="3" class="p-3 text-right">সর্বমোট প্রদেয়:</td>
                  <td class="p-3 text-right text-sm text-emerald-600">৳{{ Number(activeVoucher?.total_payable).toLocaleString('bn-BD') }}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div v-if="activeVoucher?.transaction_id" class="text-xs bg-gray-50 p-3 rounded-xl border">
            <span class="text-gray-400">ট্রানজেকশন রেফারেন্স:</span>
            <span class="font-mono font-bold text-gray-800 ml-2">{{ activeVoucher.transaction_id }}</span>
          </div>

          <div class="flex items-center justify-between pt-4 border-t text-xs">
            <div class="text-center">
              <div class="w-32 border-b border-gray-300 pb-1 mb-1"></div>
              <span class="text-gray-400">হিসাবরক্ষক</span>
            </div>
            <div class="text-center">
              <div class="w-32 border-b border-gray-300 pb-1 mb-1"></div>
              <span class="text-gray-400">সম্পাদক / প্রকাশক</span>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-2 print:hidden">
            <button @click="showVoucherModal = false" class="px-4 py-2 border rounded-xl text-gray-600 hover:bg-gray-50">বন্ধ করুন</button>
            <button @click="printVoucher" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-semibold flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
              প্রিন্ট রসিদ
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  bills: Object,
  reporters: Array,
  stats: Object,
  filters: Object,
})

const filterMonth = ref(props.filters?.month || '')
const filterStatus = ref(props.filters?.status || '')

function applyFilter() {
  router.get(route('module.ReporterBilling.index'), {
    month: filterMonth.value,
    status: filterStatus.value,
  }, { preserveState: true })
}

function resetFilter() {
  filterMonth.value = ''
  filterStatus.value = ''
  router.get(route('module.ReporterBilling.index'))
}

// Create Modal State
const showCreateModal = ref(false)
const isCalculating = ref(false)

const form = ref({
  reporter_id: '',
  billing_month: new Date().toISOString().slice(0, 7), // current YYYY-MM
  rate_per_article: 200,
  total_articles: 0,
  bonus_amount: 0,
  deduction_amount: 0,
  payment_method: 'bkash',
  account_number: '',
  notes: '',
})

function openCreateModal() {
  form.value = {
    reporter_id: props.reporters?.[0]?.id || '',
    billing_month: new Date().toISOString().slice(0, 7),
    rate_per_article: 200,
    total_articles: 0,
    bonus_amount: 0,
    deduction_amount: 0,
    payment_method: 'bkash',
    account_number: '',
    notes: '',
  }
  showCreateModal.value = true
  if (form.value.reporter_id) {
    autoCalculate()
  }
}

async function autoCalculate() {
  if (!form.value.reporter_id || !form.value.billing_month) return
  isCalculating.value = true
  try {
    const res = await fetch('/dashboard/m/ReporterBilling/calculate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      body: JSON.stringify({
        reporter_id: form.value.reporter_id,
        billing_month: form.value.billing_month,
        rate_per_article: form.value.rate_per_article,
      })
    })
    const data = await res.json()
    if (data.total_articles !== undefined) {
      form.value.total_articles = data.total_articles
    }
  } catch (e) {
    console.error(e)
  } finally {
    isCalculating.value = false
  }
}

function recalc() {
  // Trigger reactive updates
}

const netPayable = computed(() => {
  const base = (form.value.total_articles || 0) * (form.value.rate_per_article || 0)
  const net = base + (Number(form.value.bonus_amount) || 0) - (Number(form.value.deduction_amount) || 0)
  return Math.max(0, net)
})

function submitBill() {
  router.post(route('module.ReporterBilling.store'), form.value, {
    onSuccess: () => {
      showCreateModal.value = false
    }
  })
}

// Status Modal State
const showStatusModal = ref(false)
const selectedBillId = ref(null)
const statusForm = ref({
  status: 'paid',
  transaction_id: '',
  payment_method: 'bkash',
})

function openStatusModal(bill) {
  selectedBillId.value = bill.id
  statusForm.value = {
    status: bill.status || 'paid',
    transaction_id: bill.transaction_id || '',
    payment_method: bill.payment_method || 'bkash',
  }
  showStatusModal.value = true
}

function submitStatusUpdate() {
  router.patch(route('module.ReporterBilling.update-status', selectedBillId.value), statusForm.value, {
    onSuccess: () => {
      showStatusModal.value = false
    }
  })
}

// Voucher Modal State
const showVoucherModal = ref(false)
const activeVoucher = ref(null)

function openVoucherModal(bill) {
  activeVoucher.value = bill
  showVoucherModal.value = true
}

function printVoucher() {
  window.print()
}

function deleteBill(id) {
  if (confirm('আপনি কি নিশ্চিত যে এই বিল রেকর্ডটি মুছে ফেলতে চান?')) {
    router.delete(route('module.ReporterBilling.destroy', id))
  }
}
</script>

<style>
@media print {
  body * {
    visibility: hidden;
  }
  #printVoucherArea, #printVoucherArea * {
    visibility: visible;
  }
  #printVoucherArea {
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    margin: 0;
    padding: 20px;
    box-shadow: none;
    border: none;
  }
}
</style>
