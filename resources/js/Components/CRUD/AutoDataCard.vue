<template>
    <data-card>
        <template #title>
            <slot name="title">
                <div :title="title">
                    {{ title }}
                </div>
            </slot>
        </template>
        <template #actions v-if="Object.values(actions).length === 0 || Object.values(actions).filter(action=>action).length" >
            <slot name="actions">
                <crud-actions :actions="actions" :data="data" :base-url="baseUrl"/>
            </slot>
        </template>
        <div v-for="(field, index) in fields" class="col-span-3 truncate">
            <div class="text-xs text-gray-500">
                {{ field.label }}
            </div>
            <component v-if="field.component" :is="field.component" v-bind="{[modelName]: data, data }">
                {{ field.transform(data[field.label]) }}
            </component>
            <template v-else>
                <vue-json-pretty
                    v-if="isObject(data[field.label]) && field.transformNoop"
                    :data="data[field.label]"
                />
                <span
                    v-else
                    :title="field.transform(data[field.label])"
                >
                    {{
                         field.transform(data[field.label])
                    }}
                </span>
            </template>
        </div>
    </data-card>
</template>

<script>
import { library } from "@fortawesome/fontawesome-svg-core";
import { faEllipsisH } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import DataCard from "./DataCard";
import { isObject } from "lodash";
import CrudActions from "./CrudActions";
import VueJsonPretty from "vue-json-pretty";
import "vue-json-pretty/lib/styles.css";
import {getFields} from "./getFields";

library.add(faEllipsisH);

export default {
    components: {
        FontAwesomeIcon,
        VueJsonPretty,
        DataCard,
        CrudActions
    },
    props: {
        data: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
        },
        update: {
            type: Boolean,
            default: true,
        },
        delete: {
            type: Boolean,
            default: true,
        },
        modelName: {
            type: String,
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        actions: {
            type: Object,
            default: {}
        },
        baseUrl:{
            type: String,
        }
    },
    setup(props) {
        let __title;
        if (props.titleField) {
            __title = props.data[props.titleField];
        } else {
            __title = props.data.name || props.data.id;
        }

        const fields = getFields(props);

        let titleKey = props.titleField;
        if (!titleKey) {
            titleKey = props.data.name ? "name" : "id";
        }

        let __baseUrl = props.baseUrl || props.modelNamePlural || props.modelName+ 's';

        return { fields, isObject, title: __title, baseUrl:__baseUrl };
    },
};
</script>
