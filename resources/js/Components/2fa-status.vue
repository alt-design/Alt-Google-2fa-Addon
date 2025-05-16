<template>

    <div class="publish-field">
        <div class="flex items-center justify-start w-full px-1 pt-2 mb-2" :class="[(stage > 1) ? 'text-gray-500 pointer-events-none' : 'cursor-pointer']" @click="handleClick(1)">
            <span class="flex w-4 h-4 mr-3 rounded-full" :class="'bg-' + colour"></span>
            <span class="text-base" :class="'text-' + colour">{{ status }}</span>
        </div>
    </div>

</template>

<script>
export default {

    mixins: [Fieldtype],
    props: ['value', 'meta'],

    mounted()
    {
        this.setData(this.values);
    },

    data() {
        return {
            has2FAEnabled: false,
            hasOptIn: false,
            status: '',
            colour: ''
        };
    },

    computed: {
        values() {
            return Statamic.$store.state.publish['base'].values;
        }
    },

    watch: {
        values: {
            handler(newVal, oldVal) {
                this.setData(newVal);
            },
            deep: true
        }
    },

    methods : {

        setData(data)
        {
            this.has2FAEnabled = data?.enabled_2fa ?? false;
            this.hasOptIn = data?.user_opt_in ?? false;
            this.calculateStatus();
        },

        calculateStatus()
        {
            if (this.hasOptIn) {
               if (!this.has2FAEnabled) {
                   this.status = 'Pending Enrollment'
                   this.colour = 'amber-300'
                   return;
               }
                this.status = 'Enrolled'
                this.colour = 'cpGreen'
                return;
            }

            this.colour = 'slate-400'
            if (!this.has2FAEnabled) {
                this.status = 'Not Enrolled'
                return;
            }

            this.status = 'Enrolled - Inactive'
            return;
        }
    }
};
</script>
