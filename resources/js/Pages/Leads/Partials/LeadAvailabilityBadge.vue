<template>
    <div
        class="badge"
        :class="{ 'cursor-pointer': status === 'Available', ...badgeClass }"
        @click="handleClick"
    >
        {{ assigning ? "Assigning..." : status }}
    </div>
</template>

<script>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";

export default {
    props: {
        value: {
            type: Object,
            required: true,
        },
        data: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
        },
        modelName: {
            type: String,
            default: "record",
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        actions: {
            type: Object,
            default: {},
        },
    },
    setup(props) {
        const page = usePage();

        const assigning = ref(false);

        const status = computed(() => {
            if (assigning.value === true) {
                return "Assigning...";
            }

            const claimed = props.value;
            const yours = props.value === page.props.value.user.id;
            if (yours) {
                return "Yours";
            } else if (claimed) {
                return "Claimed";
            }
            return "Available";
        });

        const badgeClass = computed(() => {
            switch (status.value) {
                case "Yours":
                    return { "badge-info": true };
                case "Assigning...":
                    return { "badge-warning": true };
                case "Available":
                    return { "badge-success": true };
                case "Claimed":
                    return { "badge-error": true };
            }
        });

        const handleClick = async () => {
            console.log("handleClick");
            if (status.value === "Available") {
                assigning.value = true;
                Inertia.visit(route("data.leads.assign"), {
                    method: "post",
                    data: {
                        lead_id: props.data.id,
                        user_id: page.props.value.user.id,
                    },
                    preserveScroll: true,
                    onFinish: () => {
                        assigning.value = false;
                    },
                });
            }
        };
        return { status, handleClick, badgeClass };
    },
};
</script>
