<script setup lang="ts">
import { Bar } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from "chart.js";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
);

const baseOptions = {
  responsive: true,
  plugins: {
    legend: {
      position: "bottom",
    },
    datalabels: {
      display: false,
      color: "#000",
      font: {
        weight: "bold",
        size: 12,
      },
      align: "center", // ← لضبط النص داخل العنصر
      anchor: "center", // ← لضبط موقع النص نسبةً للعُنصر
    },
    customLabels: {
      display: false,
      color: "#000",
      font: {
        weight: "bold",
        size: 12,
      },
    },
  },
};

ChartJS.register({
  id: "customLabels",
  afterDatasetDraw(chart, args, options) {
    const meta = chart.getDatasetMeta(0);

    // if (meta.type === "bar") return;
    // if (meta.type === "pie") return;

    const {
      ctx,
      chartArea: { width, height },
    } = chart;

    meta.data.forEach((arc, index) => {
      const { x, y } = arc.tooltipPosition();
      const label = chart.data.labels?.[index] || "";
      const value = chart.data.datasets[0].data[index];
      ctx.save();
      ctx.fillStyle = options.color || "#000";
      ctx.font = `${options.font?.weight || "bold"} ${
        options.font?.size || 14
      }px sans-serif`;
      ctx.textAlign = "center";
      ctx.textBaseline = "middle";
      // ctx.fillText(`${label}: ${value}`, x, y);
      ctx.fillText(`${value}`, x, y);
      ctx.restore();
    });
  },
});

const props = defineProps<{
  period: Period;
  range: Range;
  monthlyStudentCount?: any[]; // Optional prop for passing pre-fetched stats
}>();
</script>

<template>
  <div class="w-full">
    <h2 class="text-xl font-bold mb-2">عدد الطلاب المسجلين في كل شهر</h2>
    <p class="text-sm text-muted mb-4">
      يعرض هذا الرسم البياني عدد الطلاب المسجلين في كل شهر، مما يوفر رؤى حول
      التغيرات الشهرية في أعداد الطلاب ويساعد على تتبع التقدم الأكاديمي
      والتسجيل.
    </p>
    <Bar :data="monthlyStudentCount" :options="baseOptions" />
  </div>
</template>

<style scoped></style>
