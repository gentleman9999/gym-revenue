<template>
    <div class="w-full md:w-1/2 px-2">
        <Card
            class="h-80"
            :options="{
                borderedTitle: true,
                favorite: true,
                collapse: true,
            }"
        >
            <template #cardTitle
                ><div class="text-white">Running Totals</div></template
            >
            <div class="flex flex-col">
                <select-box :items="items" label="$ Gained" />
                <img
                    src="/img/mocks/attrition_page_1.png"
                    class="px-4 pt-11 w-fit m-auto"
                />
            </div>
        </Card>
    </div>
</template>
<script setup>
import Card from "@/Components/Card";
import SelectBox from "@/Components/SelectBox";

const items = ["$ Lost", "Units Gained", "Units Lost"];
</script>
