<template>
    <div class="login">
        <VForm
            @submit.prevent
            @keyup.enter="login">
            <VCard
                variant="elevated"
                width="400"
                class="login-form pa-4">
                <template #title>
                    {{ title }}
                </template>
                <template #text>
                    <VTextField
                        v-model="username"
                        class="mb-4"
                        prepend-inner-icon="mdi-account-outline"
                        :label="$t('Username')" />
                    <VTextField
                        v-model="password"
                        class="mb-4"
                        prepend-inner-icon="mdi-lock-outline"
                        type="password"
                        :label="$t('Password')" />
                    <VBtn
                        prepend-icon="mdi-login-variant"
                        :disabled="loading"
                        :loading="loading"
                        :block="true"
                        color="deep-orange"
                        variant="flat"
                        @click.prevent="login">
                        {{ $t('Log in') }}
                    </VBtn>
                </template>
            </VCard>
        </VForm>
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
    computed: {
        title() {
            return document.title
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
