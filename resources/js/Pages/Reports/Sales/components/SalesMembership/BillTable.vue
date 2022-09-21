<template>
    <div class="md:w-1/2 m-auto">
        <div class="flex flex-row text-center">
            <div class="text-secondary w-1/3">Bill Type</div>
            <div class="text-secondary w-1/3">Quantity</div>
            <div class="text-secondary w-1/3">Total</div>
        </div>
        <div class="border border-secondary rounded pt-2 pb-4 mt-3">
            <div
                class="flex flex-row mt-2"
                v-for="(row, ndx) in data"
                :key="ndx"
            >
                <div
                    v-for="(item, index) in row"
                    :key="ndx + '-' + index"
                    class="text-center w-1/3"
                >
                    {{ item }}
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
const data = [
    ["100$", 30, "$3,000"],
    ["50$", 20, "$1,000"],
    ["10$", 50, "$500"],
    ["5$", 30, "$150"],
    ["1$", 10, "$10"],
];
</script>
