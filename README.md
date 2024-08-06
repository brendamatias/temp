# NF CONTROL ACTIVITIES

Full-stack invoice control
This project is a functional, easily testable prototype for managing invoices.
Using Angular (frontend), Nestjs (backend), PostgreSQL (database) and TypeScript across the entire stack.

# TARGET AUDIENCE

Formalized entrepreneurs registered as Individual Microentrepreneurs (MEI) who are looking for an automated way to organize ther revenue (invoice generation) and 
avoid unexpected tax liabilities at the end of the year.

## Project goals

### Main Screen
- [] **Login:** This is the first screen and displayed after the user logs in.
- [] **Menu:** Contains options for user preferences and transaction history.
- [] **Quick Actions:**
  - [] Button to create a new Invoice (NF)
  - [] Button to register a new Expense.
- [] **Dashboards:** Provide a quick overview of financial status, including:
  - [] Chart indicating how much revenue is still available to issue invouces without exceeding MEI limits.
  - [] Monthly chart of total invoices amounts generated.
  - [] Monthly chart of total expenses.
  - [] Simple balance chart (revenue - expenses, month by month).
  - [] Expense breakdown by category.
- [] **Year Selection:** Allows choosing a specific year to view historical data.

### Preferences
  - [] In the **Preferences** menu, users can manage partner companies, expense categories, and system settings.

### Partner Companies
  - [] Add and edit partner companies (clients for whom services are provided).
  - [] Required fields for each company: 
      - **CNPJ**
      - **Company Name**
      - **Corporate Name**

### Expense categories
  - [] Add and edit expense categories
  - [] Each category requires a **name** and **description**
  - [] Categories can be archived so they no longer appear when registering a new expense.

### System Settings
  - [] Define the **Annual MEI revenue limit** (currently R$81,000.00).
  - [] Enable or disable **revenue alert notifications** via e-mail and SMS.



## Getting started

To make it easy for you to get started with GitLab, here's a list of recommended next steps.
Already a pro? Just edit this README.md and make it your own. Want to make it easy? [Use the template at the bottom](#editing-this-readme)!

## Add your files

- [ ] [Create](https://docs.gitlab.com/ee/user/project/repository/web_editor.html#create-a-file) or [upload](https://docs.gitlab.com/ee/user/project/repository/web_editor.html#upload-a-file) files
- [ ] [Add files using the command line](https://docs.gitlab.com/ee/gitlab-basics/add-file.html#add-a-file-using-the-command-line) or push an existing Git repository with the following command:

```
cd existing_repo
git remote add origin https://git.vibbra.com.br/lucas-1667836337/nf-control-activities.git
git branch -M main
git push -uf origin main
```

## Test and Deploy

Use the built-in continuous integration in GitLab.

- [ ] [Get started with GitLab CI/CD](https://docs.gitlab.com/ee/ci/quick_start/index.html)
- [ ] [Analyze your code for known vulnerabilities with Static Application Security Testing(SAST)](https://docs.gitlab.com/ee/user/application_security/sast/)
- [ ] [Deploy to Kubernetes, Amazon EC2, or Amazon ECS using Auto Deploy](https://docs.gitlab.com/ee/topics/autodevops/requirements.html)
- [ ] [Use pull-based deployments for improved Kubernetes management](https://docs.gitlab.com/ee/user/clusters/agent/)
- [ ] [Set up protected environments](https://docs.gitlab.com/ee/ci/environments/protected_environments.html)

## Issue an Invoice (NF)

To create a new Invoice (NF), the user must first select a **partner company**. 
This can be done by searching for the company's **CNPJ** or **Company Name**.

Once the company is selected, the following details must be provided:
 
 - [] **Invoice Amount**
 - [] **Invouce Number**
 - [] **Service Description**
 - [] **Reference Month** (the month in which the service was provided)
 - [] **Payment Date**

After an Invoice is created, the user can later **edit any field** or **delete the record** if necessary.

## Register an Expense

To create a new Expense, the user must first select an **expense category**. The category can be found by searching for its name.

Once the category is selected, the following details must be provided:

  - [] **Expense Amount**
  - [] **Expense Name**
  - [] **Payment Date**
  - [] **Reference Month**

It's also possible to **Link the expense to a partner company** for which the service was provided.
After an Expense is created, the user can later **edit any field** or **delete the record** if needed.

## Revenue Notifications

You can configure **revenue alerts** to be notified as you approach the annual MEI revenue limit.
  
  - [] **Channels:** All notifications can be sent via **email** or **SMS**, as defined in **Preferences**.
  - [] **Monthly Limit Reminder (on the 1st):** On the **1st day of every month**, the system sends a summary showing how much revenue can still be issued in invoices without causing MEI disqualification.
  - [] **Threshold Alert (80% of annual limit):** When total revenue reaches **80%** of the MEI annual cap, the user receives an alert indicating that they are close to the limit, including guidance on steps to avoid penalties and MEI disqualification.

> Note: The annual MEI revenue limit is configurable in **Preferences** (default: R$ 81,000.00).

# Data

In that condition we can apply core helpers inside database to improve facilities on frontend and backend, so basic core rules are applied in our database **postgreSQL** sometimes.
This knowlegde are possible cause i use in many personal projetcs.
This SQL data just maintain our data STRUCTURE rule, in that project we will use seeds and migrations for academy purpose.

## JSON Data
This json represents our expected structure for this project

```
  {
    "users": [
      {
        "id": "uuid",
        "email": "string",
        "phone": "string",
        "password": "reserved string",
        "fullName": "string",
        "isActive": "boolean",
        "settings": {
          "meiAnnualLimit": "number",
          "monthlyReminderDay": "number",
          "revenueThresholdRatio": "number",
          "notifyEmail": "boolean",
          "notifySms": "boolean"
        },
        "partnersCompanies": [
          {
            "id": "uuid",
            "taxId": "string",
            "companyName": "string",
            "corporateName": "string",
            "notes": "string"
          }
        ],
        "expenseCategories": [
          {
            "id": "uuid",
            "name": "string",
            "description": "string",
            "isArchived": "boolean"
          }
        ],
        "invoices": [
          {
            "id": "uuid",
            "partnerId": "(related) uuid",
            "invoiceNumber": "string",
            "amount": "number",
            "serviceDesc": "string",
            "referenceMonth": "date",
            "paymentDate": "date"
          }
        ],
        "expenses": [
          {
            "id": "uuid",
            "cathegoryId": "(related) uuid",
            "partnerId": "(related) uuid",
            "name": "string",
            "amount": "number",
            "referenceMonth": "date",
            "paymentDate": "date"
          }
        ],
        "notifications": [
          {
            "id": "uuid",
            "type": "string",
            "channel": "(SMS || EMAIL)"
            "payload": {
              "percentUsed": "number",
              "meiAnnualLimit": "number DEFAULT (81000.00)",
              "instructions": "string"
            },
            "sentAt": "date"
          }
        ]
      }
    ]
  }
```

### SQL Helpers and Extensions

```
  -- Extensions (UUID + trigram) --
  CREATE EXTENSION IF NOT EXISTS pgcrypto;    -- for gen_random_uuid() ** Security purpose
  CREATE EXTENSION IF NOT EXISTS pg_trgm;     -- faster ILIKE searchs

  -- Helpers --
  CREATE DOMAIN money_br AS NUMERIC(14, 2) CHECK (VALUE >= 0); -- Money stored as NUMERIC to avoid float rouding issues

```

# Docker 

```
  docker exec -it nfmei_postgres psql -U postgres -d nfmei
```
