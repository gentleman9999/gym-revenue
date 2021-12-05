<template >
    <thead>
    <tr>
        <th v-for="header in headers">{{header}}</th>
    </tr>
    </thead>
</template>

<script setup>
const props = defineProps({headers:{type: Array}})
</script>
