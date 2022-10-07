<template>
    <div class="btn-group" v-if="pagination.last_page > 1">
        <template v-for="(page, ndx) in pages" :key="ndx">
            <div
                class="btn btn-outline hover:text-base-content"
                :class="{
                    'bg-primary': page === pagination.current_page,
                    'btn-disabled disabled':
                        (ndx === 0 && pagination.current_page === 1) ||
                        (ndx === pages.length - 1 &&
                            pagination.current_page === pagination.last_page),
                }"
                @click="updatePage(page, ndx)"
            >
                {{ page }}
            </div>
        </template>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    pagination: Object,
});

const pages = computed(() => {
    let temp = [props.pagination.current_page];
    props.pagination.current_page > 1 &&
        temp.unshift(props.pagination.current_page - 1);
    props.pagination.current_page < props.pagination.last_page &&
        temp.push(props.pagination.current_page + 1);
    return ["« Previous", ...temp, "Next »"];
});

const emit = defineEmits(["update-page"]);
const updatePage = (page, ndx) => {
    const { current_page, last_page } = props.pagination;
    if (page === current_page) return;
    if (
        (ndx === 0 && current_page === 1) ||
        (ndx === pages.value.length - 1 && current_page === last_page)
    )
        return;

    let updated = page;
    if (ndx === 0) updated = current_page - 1;
    else if (ndx === pages.value.length - 1) updated = current_page + 1;
    emit("update-page", updated);
};
</script>
