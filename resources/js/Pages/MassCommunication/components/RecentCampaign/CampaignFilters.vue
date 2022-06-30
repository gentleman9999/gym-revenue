<template>
    <div class="flex flex-col items-end space-y-4">
        <div class="flex flex-row space-x-4">
            <Button secondary size="xs">Category</Button>
            <select-box label="Category" :items="categories" size="xs" />
        </div>
        <div class="flex flex-row space-x-4">
            <select-box label="Created By" :items="categories" size="xs" />
            <select-box label="Sub Category" :items="categories" size="xs" />
            <select-box label="Status" :items="categories" size="xs" />
        </div>
    </div>
</template>
<script setup>
import Button from "@/Components/Button.vue";
import SelectBox from "@/Components/SelectBox";

const categories = new Array(4).fill("Category");
</script>
