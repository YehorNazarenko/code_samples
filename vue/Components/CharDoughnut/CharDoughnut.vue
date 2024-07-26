<script>
import {Bar} from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  ArcElement,
  registerables,
} from 'chart.js'
import {computed} from "vue";


ChartJS.register(Title, Tooltip, ArcElement, ...registerables);

export default {
  name: 'IncidentsCharDoughnut',
  components: {Bar},
  props: {
    title: String,
    statisticData: Array,
  },
  data() {
    return {
      data: computed(() => this.getData()),
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: this.title,
          },
        },
      },
    };
  },
  methods: {
    getData() {
      return {
        datasets: [{
          data: this.statisticData,
          backgroundColor: [
            '#F44336',
            '#ffeb3b',
          ],
        }]
      };
    },
  },
}
</script>

<template>
  <Bar type="doughnut" :data="data" :options="options"/>
</template>
