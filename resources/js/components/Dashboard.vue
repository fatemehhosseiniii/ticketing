<script setup>
import {ref, onMounted} from 'vue'
import {useRouter} from "vue-router";
import {myFetch} from "../action.js";

const tickets = ref([])
const paginate = ref([])
const router = useRouter()

const form = ref({
    subject: '',
    description: '',
    file_src: null
})

const showModal = ref(false)
const showCreateModal = ref(false)
const selectedTicket = ref(null)

const showRejectModal = ref(false)
const rejectSelectedTicket = ref(null)
const reject_description = ref('')

const errors = ref({})
const loading = ref(false)

const user = JSON.parse(localStorage.getItem('user'))


/** Get Tickets From API **/
const fetchTickets = async (page = 1) => {

    loading.value = true

    const query = new URLSearchParams({
        page: page
    })

    const data = await myFetch(`/api/dashboard/tickets?${query.toString()}`, true)

    loading.value = false
    if (data && data.status === 'success' && data.data) {
        if (data.data.tickets.length) {
            tickets.value = data.data.tickets
            paginate.value = data.data.links
        } else {
            console.log('empty')
        }
    }
}

const viewTicket = async (id) => {

    const data = await myFetch('/api/dashboard/tickets/' + id, true)
    if (data && data.status === 'success' && data.data && data.data.ticket)
        openModal(data.data.ticket)
}

/** Save new Ticket **/
const createTicket = async () => {

    //local validation
    errors.value = {}
    if (!form.value.subject || !form.value.description) {
        let errorMessages = {}
        if (!form.value.subject)
            errorMessages.subject = ['Subject is Required!']
        if (!form.value.description)
            errorMessages.description = ['Description is Required!']

        errors.value = errorMessages
        return
    }

    //make form Data
    const formData = new FormData()
    formData.append('subject', form.value.subject)
    formData.append('description', form.value.description)
    if (form.value.file_src) {
        formData.append('file_src', form.value.file_src)
    }


    //send Data to API
    const data = await myFetch('/api/dashboard/tickets', true, 'POST', formData)

    if (!data.data && data.errors) {
        if (data.errors && typeof data.errors === 'object') {
            Object.entries(data.errors).forEach(([field, messages]) => {
                errors.value = data.errors
            })
        }
        return false
    }
    if (data && data.status === 'success' && data.data.ticket) {
        tickets.value.unshift(data.data.ticket)
        paginate.value.total = (paginate.value.total ?? 0) + 1
    }


    form.value.subject = ''
    form.value.description = ''
    form.value.file_src = null

    showCreateModal.value = false
}

/** Remove ticket **/
const deleteTicket = async (id) => {
    const data = await myFetch('/api/dashboard/tickets/' + id, true, 'DELETE')
    if (data && data.status === 'success') {
        tickets.value = tickets.value.filter(t => t.code !== id)
        paginate.value.total = paginate.value.total - 1
    }
}


/** Ticket State **/
const rejected = async (id) => {
    const data = await myFetch('/api/dashboard/tickets/' + id+'/rejected', true, 'PATCH', JSON.stringify({status_message:reject_description.value}),true)

    if (!data.data && data.errors) {
        if (data.errors && typeof data.errors === 'object') {
            Object.entries(data.errors).forEach(([field, messages]) => {
                errors.value = data.errors
            })
        }
        return false
    }
    if (data && data.status === 'success') {
        fetchTickets(paginate.value.current_page)
        closeModal()
    }
}
const accepted = async (id) => {
    const data = await myFetch('/api/dashboard/tickets/' + id+'/accepted', true, 'PATCH')

    if (data && data.status === 'success') {
        fetchTickets(paginate.value.current_page)
        closeModal()
    }
}

/** table Actions**/
const nextPage = () => {
    if (paginate.value.current_page < paginate.value.last_page) {
        fetchTickets(paginate.value.current_page + 1)
    }
}
const prevPage = () => {
    if (paginate.value.current_page > 1) {
        fetchTickets(paginate.value.current_page - 1)
    }
}


/** Modal Functions**/
const openModal = (ticket) => {
    selectedTicket.value = ticket
    showModal.value = true
}
const closeModal = () => {
    showModal.value = false
    selectedTicket.value = null

    showCreateModal.value = false

    showRejectModal.value = false
    rejectSelectedTicket.value = null
}

const openCreateModal = () => {
    showCreateModal.value = true
}
const rejectTicketModal = async (id) => {
    showRejectModal.value = true
    rejectSelectedTicket.value = id
}


const logout = () => {
    localStorage.removeItem('token')
    router.push('/')
}

onMounted(() => {
    fetchTickets()

})
</script>

<template>
    <div class="container">
        <h2 class="title">Tickets</h2>

        <div class="text-left ">
            <p><small>User Login:</small> {{ user?.name }}</p>
            <p><small>Role:</small> {{ user?.role.label }}</p>
        </div>
        <div class="text-right">
            <button @click="openCreateModal()" v-if="user.role.key === 'user'">Create new Ticket</button>
            <button @click="logout()" class="close-button">Log out</button>
        </div>
        <div v-if="loading" class="loading">
            Loading...
        </div>
        <div v-else>
            <table class="table">
                <thead>
                <tr>
                    <th>Code</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Created date</th>
                    <th>actions</th>
                </tr>
                </thead>

                <tbody>
                <tr v-if="tickets.length" v-for="ticket in tickets" :key="ticket.code">
                    <td>{{ ticket.code }}</td>
                    <td>{{ ticket.subject }}</td>
                    <td>
                        <span class="badge" :class="ticket.status.class">
                            {{ ticket.status.label }}
                        </span>
                    </td>
                    <td>{{ ticket.created_at }}</td>
                    <td class="actions">

                        <button class="icon-btn view" @click="viewTicket(ticket.code)">
                            👁
                        </button>

                        <button
                            v-if="ticket.status.key === 'new' && user.role.key === 'user'"
                            class="icon-btn delete"
                            @click="deleteTicket(ticket.code)">
                            🗑
                        </button>

                        <button v-if="(ticket.status.key === 'new' && user.role.key === 'level_one') || (ticket.status.key === 'accepted' && user.role.key === 'level_two')"
                                @click="accepted(ticket.code)"
                                class="icon-btn success">
                            ✔
                        </button>
                        <button
                            v-if="(ticket.status.key === 'new' && user.role.key === 'level_one') || (ticket.status.key === 'accepted' && user.role.key === 'level_two')"
                            @click="rejectTicketModal(ticket.code)"
                            class="icon-btn delete">
                            ✖
                        </button>
                    </td>
                </tr>
                <tr v-else>
                    <td colspan="5"> No Data Exists!</td>
                </tr>
                </tbody>
            </table>

            <div class="pagination" v-if="paginate.last_page > 1">

                <!-- Prev -->
                <button
                    @click="prevPage"
                    :disabled="paginate.current_page === 1"
                >
                    «
                </button>

                <!-- Page Info -->
                <span>
                    page {{ paginate.current_page }}
                    of {{ paginate.last_page }}
                </span>

                <!-- Next -->
                <button
                    @click="nextPage"
                    :disabled="paginate.current_page === paginate.last_page"
                >
                    »
                </button>

            </div>

            <!-- Total -->
            <div class="total">
                Total tickets: {{ paginate.total ?? 0 }}
            </div>

        </div>


        <!-- Modal -->
        <div v-if="showModal" class="modal-overlay" @click="closeModal">
            <div class="modal" @click.stop>

                <h3>Ticket #{{ selectedTicket.code }}</h3>

                <p><b>Subject: </b> {{ selectedTicket?.subject }}</p>
                <p><b>Description: </b> {{ selectedTicket?.description }}</p>
                <p><b>Status: </b>
                    <span class="badge" :class="selectedTicket.status.class">
                        {{ selectedTicket.status.label }}
                    </span>
                    <small v-if="selectedTicket.checked_at">
                        <b>Checked date:</b> {{ selectedTicket?.checked_at }}
                    </small>
                </p>
                <p v-if="selectedTicket.file_src">
                    <a :href="selectedTicket?.file_src" target="_blank">Download file</a>
                </p>

                <div v-if="selectedTicket.status.key === 'rejected'">
                    <hr/>
                    <p class="reject">
                        <b>rejected note:</b> {{ selectedTicket?.status_message }}
                    </p>
                </div>

                <div class="w-100 text-right">
                    <button @click="closeModal" class="close-button">close</button>
                </div>

            </div>
        </div>
        <div v-if="showCreateModal" class="modal-overlay" @click="closeModal">
            <div class="modal" @click.stop>

                <h3>Create new Ticket</h3>

                <input v-model="form.subject" placeholder="Subject" @input="errors.subject = null" class="w-100"/>
                <small v-if="errors.subject" class="error-text">{{ errors.subject[0] }}</small>

                <textarea v-model="form.description" placeholder="Description" @input="errors.description = null"
                          class="w-100"></textarea>
                <small v-if="errors.description" class="error-text">{{ errors.description[0] }}</small>

                <input type="file" class="w-100" @change="form.file_src = $event.target.files[0]"/>
                <small v-if="errors.file_src" class="error-text">{{ errors.file_src[0] }}</small>

                <div class="w-100 text-right">
                    <button @click="closeModal" class="close-button">Cancel</button>
                    <button @click="createTicket" class="create-button">Create</button>
                </div>

            </div>
        </div>
        <div v-if="showRejectModal" class="modal-overlay" @click="closeModal">
            <div class="modal" @click.stop>

                <h3>Mention the reason for the ticket rejection:</h3>

                <textarea v-model="reject_description" placeholder="Reason" @input="errors.status_message = null"
                          class="w-100"></textarea>
                <small v-if="errors.status_message" class="error-text">{{ errors.status_message[0] }}</small>


                <div class="w-100 text-right">
                    <button @click="closeModal" class="close-button">Cancel</button>
                    <button @click="rejected(rejectSelectedTicket)" class="create-button">Reject</button>
                </div>

            </div>
        </div>
    </div>
</template>
