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

    mounted()
    {
        let values = this.$store.state.publish['base'].values;
        console.log(values);
        this.has2FAEnabled = values?.enabled_2fa ?? false;
        this.hasOptIn = values?.user_opt_in ?? false;
        this.calculateStatus();
    },

    data() {
        return {
            has2FAEnabled: false,
            hasOptIn: false,
            status: '',
            colour: ''
        };
    },

    methods : {
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

            if (!this.has2FAEnabled) {
                this.status = 'Not Enrolled'
                this.colour = 'slate-400'
                return;
            }

            this.status = 'Enrolled'
            this.colour = 'cpGreen'
            return;
        }
    }
};
</script>
