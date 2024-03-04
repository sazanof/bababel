<template>
    <div class="login">
        <v-form
            @submit.prevent
            @keyup.enter="login">
            <v-card class="login-form pa-4">
                <template #text>
                    <v-text-field
                        v-model="username"
                        class="mb-4"
                        prepend-inner-icon="mdi-account-outline"
                        :label="$t('Username')"
                        variant="outlined" />
                    <v-text-field
                        v-model="password"
                        class="mb-4"
                        prepend-inner-icon="mdi-lock-outline"
                        type="password"
                        :label="$t('Password')"
                        variant="outlined" />
                    <v-btn
                        prepend-icon="mdi-login-variant"
                        :disabled="loading"
                        :loading="loading"
                        :block="true"
                        color="deep-orange"
                        size="x-large"
                        variant="flat"
                        @click.prevent="login">
                        {{ $t('Log in') }}
                    </v-btn>
                </template>
            </v-card>
        </v-form>
    </div>
</template>

<script>
import { useToast } from 'vue-toastification'

const toast = useToast()
export default {
    name: 'Login',
    data() {
        return {
            loading: false,
            username: '',
            password: ''
        }
    },
    methods: {
        async login() {
            this.loading = true
            await this.$store.dispatch('logIn', {
                username: this.username,
                password: this.password
            }).catch(err => {
                toast.error(err.response.data.message)
            }).finally(() => {
                this.loading = false
            })
        },
        clearForm() {
            this.password = ''
            this.username = ''
        }
    }
}
</script>

<style lang="scss" scoped>
.login {
    position: fixed;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;

    &:after {
        content: "";
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        z-index: 11;
        background-image: url("../../img/meeting-bg.jpg");
        background-position: center;
        background-size: cover;
        overflow: hidden;
        filter: blur(2px);
        opacity: 0.7;
    }

    &:before {
        content: "";
        position: fixed;
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        z-index: 12;
        background: var(--color-orange);
        opacity: 0.4;
    }

    .login-form {
        position: relative;
        z-index: 14;
        width: 500px;
    }
}
</style>
