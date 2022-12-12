<template>
    <ApolloQuery :query="(gql) => queries['emailTemplates']" :variables="param">
        <template v-slot="{ result: { data, loading, error } }">
            <TemplateList v-if="!loading && !!data" type="email">
                <template #current_templates>
                    <EmailTemplatePreview
                        v-for="t in data.emailTemplates.data"
                        :key="t.id"
                        :template="t"
                        :trash_template="false"
                        :permissions="permissions"
                        @edit="handleTemplateEditor"
                        @trash="handleTrash"
                    />
                </template>
                <template #trashed_templates>
                    <EmailTemplatePreview
                        v-for="t in data"
                        :key="t.id + '_trash'"
                        :template="t"
                        :trash_template="true"
                        :permissions="permissions"
                        @restore="handleRestore"
                    />
                </template>
            </TemplateList>
        </template>
    </ApolloQuery>
</template>

<script setup>
import TemplateList from "./TemplateList.vue";
import EmailTemplatePreview from "./previewItems/EmailTemplatePreview.vue";
import queries from "@/gql/queries";

const permissions = {
    create: true,
    update: true,
    trash: true,
    restore: true,
    read: true,
    delete: true,
};

const handleTrash = (e) => {
    console.log("trash template id", e);
};

const handleTemplateEditor = (e) => {
    console.log("edit template", e);
};

const handleRestore = (e) => {
    console.log("restore template", e);
};
</script>
