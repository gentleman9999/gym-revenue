<template>
    <LayoutHeader title="Upload Files">
        <h2 class="font-semibold text-xl leading-tight">Upload Files</h2>
    </LayoutHeader>

    <file-manager :client-id="$page.props.user.client_id" />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import FileManager from "./Partials/FileManager.vue";
</script>
