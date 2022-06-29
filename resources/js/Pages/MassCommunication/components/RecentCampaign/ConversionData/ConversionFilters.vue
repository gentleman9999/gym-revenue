<template>
    <div class="flex flex-row space-x-4">
        <select-box label="Category" :items="categories" size="xs" /><select-box
            label="Created By"
            :items="categories"
            size="xs"
        />
        <select-box label="Sub Category" :items="categories" size="xs" />
        <select-box label="Status" :items="categories" size="xs" />
    </div>
</template>
<script setup>
import SelectBox from "@/Components/SelectBox";

const categories = new Array(4).fill("Category");
</script>
