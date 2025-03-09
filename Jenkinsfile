pipeline {
    agent any
    environment {
        AWS_REGION      = "eu-central-1"
        AWS_ACCOUNT_ID  = "225320283044"
        ECR_REPOSITORY  = "cicd-infra"
        ECR_URL         = "${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"

        // Auto-detect repo name (Assumes repo name matches app name)
        APP_NAME        = "${env.JOB_NAME}".tokenize('/')[-1]

        // Environment parameter (passed manually or from Jenkins UI)
        ENVIRONMENT     = "${params.ENVIRONMENT ?: 'dev'}"

        // Dynamic image tag
        IMAGE_TAG       = "$ENVIRONMENT-${env.BUILD_NUMBER}"

        // GitOps repository where ArgoCD watches changes
        GITOPS_REPO     = "git@github.com:Opsmiths-Technologies/opst-deploy-wordpress-app.git"
        GITOPS_BRANCH   = "main"
    }
    parameters {
        string(name: 'ENVIRONMENT', defaultValue: 'dev', description: 'Deployment environment (dev, prod, etc.)')
    }
    stages {
        stage('Checkout Code') {
            steps {
                git credentialsId: 'github-org-credentials', url: "https://github.com/Opsmiths-Technologies/${APP_NAME}.git", branch: 'main'
            }
        }
        stage('Build Docker Image') {
            steps {
                sh "docker build -t $ECR_URL/$ECR_REPOSITORY:$IMAGE_TAG ."
            }
        }
        stage('Login to ECR & Push Image') {
            steps {
                withCredentials([aws(credentialsId: 'aws-credentials', accessKeyVariable: 'AWS_ACCESS_KEY_ID', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY')]) {
                    sh """
                    aws configure set aws_access_key_id $AWS_ACCESS_KEY_ID
                    aws configure set aws_secret_access_key $AWS_SECRET_ACCESS_KEY
                    aws configure set region $AWS_REGION
                    aws ecr get-login-password --region $AWS_REGION | docker login --username AWS --password-stdin $ECR_URL
                    docker push $ECR_URL/$ECR_REPOSITORY:$IMAGE_TAG
                    """
                }
            }
        }
        stage('Update GitOps Repository for ArgoCD') {
            steps {
                script {
                    sh """
                    git clone $GITOPS_REPO
                    cd opst-deploy-wordpress-app

                    # Dynamic path based on the app name and environment
                    APP_KUSTOMIZE_PATH="kubernetes/apps/$APP_NAME/$ENVIRONMENT"

                    # Ensure the deployment file exists
                    if [ ! -f "$APP_KUSTOMIZE_PATH/kustomization.yaml" ]; then
                        echo "Error: kustomization.yaml not found in $APP_KUSTOMIZE_PATH"
                        exit 1
                    fi

                    # Update the kustomization.yaml file dynamically
                    sed -i 's|newTag:.*|newTag: $IMAGE_TAG|' "$APP_KUSTOMIZE_PATH/kustomization.yaml"

                    git config user.email "jenkins@opsmiths.com"
                    git config user.name "Jenkins CI"
                    git add "$APP_KUSTOMIZE_PATH/kustomization.yaml"
                    git commit -m "Updated $APP_NAME ($ENVIRONMENT) to image $IMAGE_TAG"
                    git push origin $GITOPS_BRANCH
                    """
                }
            }
        }
    }
}
