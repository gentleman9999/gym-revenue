<template>
    <div class="flex flex-row space-x-4">
        <Button
            primary
            size="sm"
            @click="Inertia.visitInModal(route('files.upload'))"
        >
            Upload
        </Button>
        <Button primary size="sm"> New Folder </Button>
    </div>
</template>
<script setup>
import Button from "@/Components/Button.vue";
import { Inertia } from "@inertiajs/inertia";
</script>
