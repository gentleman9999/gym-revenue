<template>
    <Card>
        <div class="flex flex-col">
            <div class="flex flex-row justify-between pb-2">
                <div class="flex flex-row space-x-2 items-center">
                    <div class="text-lg">April</div>
                    <arrow-icon direction="right" fill="#FFF" size="xs" />
                </div>
                <div class="text-white cursor-pointer text-lg">&#8943;</div>
            </div>
            <img src="/img/mocks/clalendar_page_4.png" />
        </div>
    </Card>
</template>
<script setup>
import Card from "@/Components/Card";
import ArrowIcon from "@/Components/Icons/Arrow";
</script>
