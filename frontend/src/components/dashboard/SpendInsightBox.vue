<!--
  - BillInsightBox.vue
  - Copyright (c) 2022 james@firefly-iii.org
  -
  - This file is part of Firefly III (https://github.com/firefly-iii).
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <https://www.gnu.org/licenses/>.
  -->

<template>
  <!-- TODO most left? q-mr-sm -->
  <!-- TODO middle? dan q-mx-sm -->
  <!-- TODO right? dan q-ml-sm -->
  <div class="q-mx-sm">
    <q-card bordered>
      <q-item>
        <q-item-section>
          <q-item-label><strong>{{ $t('firefly.left_to_spend') }}</strong></q-item-label>
        </q-item-section>
      </q-item>
      <q-separator/>
      <q-card-section horizontal>
        <q-card-section>
          <q-circular-progress
            :thickness="0.22"
            :value="percentage"
            color="negative"
            size="50px"
            track-color="positive"
          />
        </q-card-section>
        <q-separator vertical/>
        <q-card-section v-if="0 === budgeted.length && 0 === spent.length">
          {{ $t('firefly.no_budget') }}
        </q-card-section>
        <q-card-section v-if="budgeted.length > 0 || spent.length > 0">
          <span :title="formatAmount(this.currency, this.budgetedAmount)">{{ $t('firefly.budgeted') }}</span>:
          <!-- list budgeted -->
          <span v-for="(item, index) in budgeted">
            <span :title="formatAmount(item.native_code, item.native_sum)">{{
                formatAmount(item.code, item.sum)
              }}</span>
            <span v-if="index+1 !== budgeted.length"> + </span>
          </span>
          <br/>
          <span v-if="spent.length > 0" :title="formatAmount(this.currency, this.spentAmount)">{{
              $t('firefly.spent')
            }}: </span>
          <!-- list spent -->
          <span v-for="(item, index) in spent">
            <span :title="formatAmount(item.native_code, item.native_sum)">{{
                formatAmount(item.code, item.sum)
              }}</span>
            <span v-if="index+1 !== spent.length"> + </span></span>
        </q-card-section>
      </q-card-section>
    </q-card>
  </div>
</template>

<script>
import {useFireflyIIIStore} from "../../stores/fireflyiii";
import Sum from "../../api/v2/budgets/sum";

export default {
  data() {
    return {
      store: null,
      budgeted: [],
      spent: [],
      currency: 'EUR',
      //percentage: 0,
      budgetedAmount: 0.0,
      spentAmount: 0.0,
    }
  },
  name: "SpendInsightBox",
  computed: {
    percentage: function () {
      if (0 === this.budgetedAmount) {
        return 100;
      }
      if (0.0 === this.spentAmount) {
        return 0;
      }
      const pct = (this.spentAmount / this.budgetedAmount) * 100;
      if (pct > 100) {
        return 100;
      }
      return pct;
    }
  },
  mounted() {
    this.store = useFireflyIIIStore();

    // TODO this code snippet is recycled a lot.
    // subscribe, then update:
    this.store.$onAction(
      ({name, $store, args, after, onError,}) => {
        after((result) => {
          if (name === 'setRange') {
            this.triggerUpdate();
          }
        })
      }
    )
    this.triggerUpdate();
  },
  methods: {
    triggerUpdate: function () {
      if (null !== this.store.getRange.start && null !== this.store.getRange.end) {
        this.budgeted = [];
        const start = new Date(this.store.getRange.start);
        const end = new Date(this.store.getRange.end);
        const sum = new Sum;
        this.currency = this.store.getCurrencyCode;
        sum.budgeted(start, end).then((response) => this.parseBudgetedResponse(response.data));
        sum.spent(start, end).then((response) => this.parseSpentResponse(response.data));
      }
    },
    // TODO this method is recycled a lot.
    formatAmount: function (currencyCode, amount) {
      return Intl.NumberFormat(this.store.getLocale, {style: 'currency', currency: currencyCode}).format(amount);
    },
    parseBudgetedResponse: function (data) {
      for (let i in data) {
        if (data.hasOwnProperty(i)) {
          const current = data[i];
          const hasNative = current.converted && current.native_id !== current.id && parseFloat(current.native_sum) !== 0.0;
          this.budgeted.push(
            {
              sum: current.sum,
              code: current.code,
              native_sum: current.converted ? current.native_sum : current.sum,
              native_code: current.converted ? current.native_code : current.code,
              native: hasNative
            }
          );
          if (current.converted && (hasNative || current.native_id === current.id)) {
            this.budgetedAmount = this.budgetedAmount + parseFloat(current.native_sum);
          }
          if (!current.converted) {
            this.budgetedAmount = this.budgetedAmount + parseFloat(current.sum);
          }
        }
      }
    },
    parseSpentResponse: function (data) {
      for (let i in data) {
        if (data.hasOwnProperty(i)) {
          const current = data[i];
          const hasNative = current.converted && current.native_id !== current.id && parseFloat(current.native_sum) !== 0.0;
          this.spent.push(
            {
              sum: current.sum,
              code: current.code,
              native_sum: current.converted ? current.native_sum : current.sum,
              native_code: current.converted ? current.native_code : current.code,
              native: hasNative
            }
          );
          if (current.converted && (hasNative || current.native_id === current.id)) {
            this.spentAmount = this.spentAmount + (parseFloat(current.native_sum) * -1);
          }
          if (!current.converted) {
            this.spentAmount = this.spentAmount + (parseFloat(current.sum) * -1);
          }
        }
      }
    },
  }
}
</script>

<style scoped>

</style>
