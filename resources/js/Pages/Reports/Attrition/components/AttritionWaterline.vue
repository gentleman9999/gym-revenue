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
                ><div class="text-white">Waterline</div></template
            >
            <div class="flex flex-col">
                <img
                    src="/img/mocks/attrition_page_2.png"
                    class="pt-4 w-fit m-auto"
                />
                <div class="flex flex-row mt-4 pl-5 space-x-8">
                    <div class="flex flex-row space-x-3">
                        <rectangle-icon />
                        <span class="text-xs">New Members</span>
                    </div>
                    <div class="flex flex-row space-x-3">
                        <rectangle-icon fill="#003A73" />
                        <span class="text-xs">Renewals</span>
                    </div>
                </div>
            </div>
        </Card>
    </div>
</template>
<script setup>
import Card from "@/Components/Card";
import SelectBox from "@/Components/SelectBox";
import RectangleIcon from "@/Components/Icons/Rectangle";

const items = ["$ Lost", "Units Gained", "Units Lost"];
</script>
