<!DOCTYPE html>
<html>
<head>
    <title>RRDev CMS Installation Wizard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="CodeIgniter Installation Wizard">

    <link rel="shortcut icon" href="/favicon-32x32.png">

    <!-- Custom Theme files -->
    <link href="/css/style_wizard_sys_install.css" rel="stylesheet" type="text/css" media="all"/>
    <!-- //Custom Theme files -->

</head>
<body>
<!-- main
https://github.com/firebase/php-jwt
https://www.youtube.com/watch?v=JUzhx3TWoJw&list=PLg5SS_4L6LYstwxTEOU05E0URTHnbtA0l&index=16
https://github.com/marketplace/actions/ssh-deploy

https://github.com/php-youtube/php-invest/blob/master/application/config/routes.php
https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch

https://github.com/MubertAI/Mubert-Text-to-Music/blob/main/Mubert_Text_to_Music.ipynb
-->
<div class="main" id="install_wizard_app">

    <div class="main-agilerow">
        <div class="install-wrap">
            <h1>RRDev CMS </h1>

            <span class="subt">INSTALLATION WIZARD</span>
            <hr/>
            <h2>Install Now</h2>
            <p>RRDev CMS Installer </p>
        </div>

        <div class="form-wrap" id="main-wrap" v-if="install_state===0">

            <form action="/" method="post" @submit.prevent="submitForm">

                <h3>Step {{ current_step }} : {{ current_step_name }}</h3>

                <div class="form-step1">

                    <div class="st_input" v-for="step in steps_inputs">

                        <label>{{ step.label }}</label>
                        <span class="hints">{{ step.span }}</span>
                        <input v-if="step.type=='text'" type="text" :name="step.name" :placeholder="step.placeholder" :required="step.required" v-model="step.data">
                        <input v-if="step.type=='number'" type="text" :name="step.name" :placeholder="step.placeholder" :required="step.required" v-model="step.data">
                        <select v-if="step.type=='select'" :name="step.name" :required="step.required" v-model="step.data">
                            <option value="" disabled selected>{{ step.placeholder }}</option>
                            <option v-for="o in step.options" :value="o.value">{{ o.name }}</option>
                        </select>
                    </div>

                </div>

                <br/>

                <div id="form-progress">

                    <p v-if="errorMsg">{{ errorMsg }}</p>

                    <div v-if="inProcess">
                        <div class="loader"></div>
                        <br/>Installing. Please Wait!
                    </div>

                </div>

                <br/>

                <div id="submit_btn" v-if="!inProcess">
                    <input type="submit" value="Start Installation">
                </div>

            </form>

        </div>

        <div class="form-wrap" id="success-wrap" v-if="install_state===1">
            <h2>Installation Successful.</h2>
            <hr/>
            <p><a class="btn btn-primary" href="/user/login">Click Here</a><strong> to Login!</strong></p>
        </div>

    </div>
</div>
<!-- //main -->
<script src="https://unpkg.com/vue@next"></script>

<script>
    const install_wizard_steps = [
        {
            name: 'Database Configuration',
            inputs: [
                {
                    label: 'DB Host',
                    span: 'Ex - localhost',
                    type: 'text',
                    name: 'db_host',
                    placeholder: 'Enter Database Host',
                    required: true
                },
                {
                    label: 'DB Port',
                    span: 'Ex - 3306',
                    type: 'number',
                    name: 'db_port',
                    placeholder: 'Enter Database Port',
                    required: true
                },
                {
                    label: 'DB User',
                    span: 'Ex - root',
                    type: 'text',
                    name: 'db_user',
                    placeholder: 'Enter Database UserName',
                    required: true
                },
                {
                    label: 'DB Password',
                    span: 'Ex - Password',
                    type: 'text',
                    name: 'db_password',
                    placeholder: 'Enter Database Password',
                    required: true
                },
                {
                    label: 'DB Name',
                    span: 'Ex - db_name',
                    type: 'text',
                    name: 'db_name',
                    placeholder: 'Enter Database Name',
                    required: true
                },
                {
                    label: 'DB driver',
                    span: 'Ex - db_driver',
                    type: 'select',
                    name: 'db_driver',
                    placeholder: 'Select DB Driver',
                    required: true,
                    options:[
                        {name:"DB MariaDB",value:"MariaDB"},
                        {name:"DB PostgreSQL",value:"PostgreSQL"},
                        {name:"DB CUBRID",value:"CUBRID"},
                        {name:"DB SQLite",value:"SQLite"},
                    ]
                },

            ]
        },
        {
            name: 'Admin Configuration',
            inputs: [
                {
                    label: 'Admin E-Mail',
                    span: 'Ex - admin@email.com',
                    type: 'text',
                    name: 'admin_email',
                    placeholder: 'Enter administrator e-mail',
                    required: true
                },
                {
                    label: 'Admin login',
                    span: 'Ex - root',
                    type: 'text',
                    name: 'admin_login',
                    placeholder: 'Enter administrator login',
                    required: true
                },
                {
                    label: 'Admin Password',
                    span: 'Ex - Password',
                    type: 'text',
                    name: 'admin_password',
                    placeholder: 'Enter administrator Password',
                    required: true
                },
            ]
        }
    ];

    const install_wizard = {
        data() {
            return {
                install_state: 0,
                current_step: 1,
                current_step_name: '',
                inProcess: false,
                errorMsg: '',
                steps: [],
                steps_inputs: [],
            }
        },
        mounted() {
            this.nextStep();
        },
        methods: {
            nextStep(){
                let current_step = this.current_step - 1;
                this.steps = install_wizard_steps;
                this.steps_inputs = this.steps[current_step].inputs;
                this.current_step_name = this.steps[current_step].name;
            },
            submitForm() {
                this.inProcess = true;
                this.errorMsg = '';

                const formData = new FormData();
                this.steps_inputs.forEach(field => {
                    formData.append(field.name, field.data);
                });

                fetch('/', {
                    method: 'POST',
                    body: formData
                })
                    .then((response) => response.text())
                    .then((result) => {
                        if(result==="success"){
                            this.install_state = 1;
                        }
                        if(result==="nextstep"){
                            this.current_step++;
                            this.nextStep();
                        }
                        // console.log('Success:', result);
                        this.inProcess = false;
                        this.errorMsg = result;
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                        this.inProcess = false
                    });
            }
        }
    };

    Vue.createApp(install_wizard).mount('#install_wizard_app');
</script>

</body>
</html>