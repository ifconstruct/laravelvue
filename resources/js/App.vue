<template>
    <h1 class="alert text-center">Laravel-Vue multi-user CSV parser</h1>

        <div class="container">
            <div class="mb-3">
            <label for="formSelect" class="form-label">Active user</label>
            <select class="form-select" id="userSelect" @change="handleChange" v-model="selected" >
                <option  v-for="(user, key) in users" :key="user.id" v-bind:value="user.id"  >{{ user.name }}</option>
            </select>
            </div>
                <div class="mb-3">
                    <label for="file" class="form-label">Csv upload</label>
                    <form ref="refForm">
                        <div>
                            <input id="file" type="file" @change="select">
                            <progress :value="progress" max="100"></progress>
                        </div>
                    </form>
                </div>
        </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>User Files</h1>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Parsing status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(file, key) in files" :key="file.id" >
                    <td>{{ file.id }}</td>
                    <td>{{ file.name }}</td>
                    <td>
                        <label v-if="file.status==0">Ready</label>
                        <label v-if="file.status==1"><img :src="this.image" ></label>
                        <label v-if="file.status==2">Done</label>
                    </td>
                    <td>
                        <button v-if="file.status==0" class="btn btn-primary" v-on:click="this.Parse(file.id,file.name)">Parse</button>
                        <button v-if="file.status==2"  class="btn btn-primary" v-on:click="this.View(file.id,file.name)">View</button>
                        <button  v-if="file.status!==1" class="btn btn-danger m-lg-1" v-on:click="this.Delete(file.id)">Delete</button>
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
            <h1>Parsed file {{ this.fileName }}</h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Approved data</h1>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Col1</th>
                    <th>Col2</th>
                    <th>Col3</th>
                    <th>Col4</th>
                    <th>Col5</th>
                    <th>Col6</th>
                    <th>Col7</th>
                    <th>Col8</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(approve, key) in approved" :key="approve.id" >
                    <td>{{ approve.col_id }}</td>
                    <td>{{ approve.col1 }}</td>
                    <td>{{ approve.col2 }}</td>
                    <td>{{ approve.col3 }}</td>
                    <td>{{ approve.col4 }}</td>
                    <td>{{ approve.col5 }}</td>
                    <td>{{ approve.col6 }}</td>
                    <td>{{ approve.col7 }}</td>
                    <td>{{ approve.col8 }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Failure data</h1>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Col1</th>
                    <th>Col2</th>
                    <th>Col3</th>
                    <th>Col4</th>
                    <th>Col5</th>
                    <th>Col6</th>
                    <th>Col7</th>
                    <th>Col8</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(fail, key) in failure" :key="fail.id" >
                    <td>{{ fail.col_id }}</td>
                    <td>{{ fail.col1 }}</td>
                    <td>{{ fail.col2 }}</td>
                    <td>{{ fail.col3 }}</td>
                    <td>{{ fail.col4 }}</td>
                    <td>{{ fail.col5 }}</td>
                    <td>{{ fail.col6 }}</td>
                    <td>{{ fail.col7 }}</td>
                    <td>{{ fail.col8 }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>

import myImg from '@/assets/loading.gif';
import axios from "./axios.js";
import toastr from "toastr";
export default {

    name: "App",

    watch: {
        chunks: {
            handler(val, oldVal) {
                if(val.length>0){
                    this.upload();
                }
            },
            deep: true
        },
    },

    computed: {

        progress() {
            if(this.file) {
                return Math.floor((this.uploaded * 100) / this.file.size);
            }
        },

        formData() {
            let formData = new FormData;

            if(this.chunks.length === 1){
                this.getFiles(this.selected);
                this.setValues();
                this.clearFile();
                toastr.success("File uploaded");
            }

            formData.set('is_last', this.chunks.length === 1);
            formData.set('file', this.chunks[0], `${this.file.name}.part`);
            formData.set('user_id', this.selected );

            return formData;
        },
        config() {
            return {
                method: 'POST',
                data: this.formData,
                url: 'api/upload',
                headers: {
                    'Content-Type': 'application/octet-stream'
                },
                onUploadProgress: (event) => {
                    this.uploaded += event.loaded;
                }
            };
        }
    },

    data() {
        return {
            selected: 1,
            files: [],
            approved : [],
            failure : [],
            pApproved : 0,
            pFailure : 0,
            fileName : "",
            image : myImg,
            images: null,
            users: [],
            file: null,
            chunks: [],
            uploaded: 0
        };
    },

    mounted() {
        this.getUsers();
        this.handleChange();
        window.setInterval(() => {
           this.getFiles(this.selected);
        }, 3000)
    },

    methods: {

        select(event) {
                this.file = event.target.files.item(0);
                console.log(this.chunks.length)
                this.createChunks();
        },

        upload() {
            axios(this.config).then(response => {
                this.chunks.shift();
            }).catch(error => {
                console.log(error);
            });
        },

        createChunks() {

            let size = 1000000, chunks = Math.ceil(this.file.size / size);

            for (let i = 0; i < chunks; i++) {
                this.chunks.push(this.file.slice(
                    i * size, Math.min(i * size + size, this.file.size), this.file.type
                ));
            }
        },

        handleChange() {         ;
            this.getFiles(this.selected);
            this.setValues();
            this.clearFile();
        },

        setValues(){
            this.approved = [];
            this.failure = [];
            this.fileName = "";
            this.pApproved = 0;
            this.pFailure = 0;
        },

        clearFile(){
            document.getElementById('file').value= null;
        },

        View(id,name){
            this.pApproved = 0;
            this.pFailure = 0;
            this.getFailure(id);
            this.getApproved(id);
            this.fileName = name;
        },

        async Delete(id){
            let res = await axios.get(`/api/deleteFiles/${id}`);
            this.getFiles(this.selected);
            toastr.success("File deleted");
            this.getFailure(id);
            this.getApproved(id);
        },

        ChangeStatus(id){
            axios.get(`/status/${id}`).then((res) => {
                this.getFiles(this.selected);
            }) ;
        },

        async Parse(id,name){

            toastr.success("File parsing , wait for status changed");
            this.ChangeStatus(id);

             axios.get(`/go/${id}`).then((res) => {
            }) ;

        },

        async getUsers() {
            let res = await axios.get(`/api/getUsers`);
            this.users = res.data.users;
        },

        async getApproved(id) {
            let res = await axios.get(`/api/getApproved/${id}/${this.pApproved}`);
            this.approved = res.data.approved;
        },

        async getFailure(id) {
            let res = await axios.get(`/api/getFailure/${id}/${this.pFailure}`);
            this.failure = res.data.failure;
        },

        async getFiles(id) {
            let res = await axios.get(`/api/getFiles/${id}`);
            this.files = res.data.files;
        },
    },

};
</script>
