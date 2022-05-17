<template>
    <dashboard-widget
        title="Sales"
        :modal-component="SalesModal"
        :amount="18000"
    >
        <div class="flex flex-row">
            <div class="w-1/3 text-secondary flex flex-col gap-2 items-center">
                <div>Stat 1</div>
                <div>Stat 2</div>
                <div>Stat 3</div>
                <div>Stat 4</div>
            </div>
            <div class="w-2/3">
                <svg
                    id="Group_473"
                    data-name="Group 473"
                    xmlns="http://www.w3.org/2000/svg"
                    width="109.515"
                    height="109.511"
                    viewBox="0 0 109.515 109.511"
                >
                    <path
                        id="Path_2615"
                        data-name="Path 2615"
                        d="M0-62.5A54.756,54.756,0,0,1,44.3-39.929a54.756,54.756,0,0,1,7.777,49.1L40.411,5.386A42.49,42.49,0,0,0,34.375-32.72,42.49,42.49,0,0,0,0-50.235Z"
                        transform="translate(54.759 62.5)"
                        fill="#d78136"
                    />
                    <path
                        id="Path_2616"
                        data-name="Path 2616"
                        d="M44.71,18.777A54.756,54.756,0,0,1-7.365,56.613,54.756,54.756,0,0,1-59.441,18.777l11.665-3.79A42.49,42.49,0,0,0-7.365,44.347a42.49,42.49,0,0,0,40.411-29.36Z"
                        transform="translate(62.125 52.898)"
                        fill="#fc371d"
                    />
                    <path
                        id="Path_2617"
                        data-name="Path 2617"
                        d="M-59.821,10.655A54.756,54.756,0,0,1-39.929-50.564l7.209,9.923A42.49,42.49,0,0,0-48.156,6.865Z"
                        transform="translate(62.504 61.021)"
                        fill="#f27e92"
                    />
                    <path
                        id="Path_2618"
                        data-name="Path 2618"
                        d="M-36.737-52.043A54.756,54.756,0,0,1-4.552-62.5v12.265A42.49,42.49,0,0,0-29.527-42.12Z"
                        transform="translate(59.311 62.5)"
                        fill="#cdcdce"
                    />
                    <text
                        id="_178"
                        data-name="178"
                        transform="translate(53.981 64.137)"
                        fill="#fff"
                        font-size="24"
                        font-family="Muli-Bold, Muli"
                        font-weight="700"
                    >
                        <tspan x="-21.6" y="0">178</tspan>
                    </text>
                </svg>
            </div>
        </div>

        <div class="text-secondary font-bold">Total: 100%</div>
    </dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import SalesModal from "@/Pages/Reports/Sales/SalesModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        return { SalesModal };
    },
});
</script>
