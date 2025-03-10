pipeline {
    agent any
    environment {
        AWS_REGION      = "eu-central-1"
        AWS_ACCOUNT_ID  = "225320283044"
        ECR_REPOSITORY  = "cicd-infra"
        ECR_URL         = "${AWS_ACCOUNT_ID}.dkr.ecr.${AWS_REGION}.amazonaws.com"

        // Environment parameter (passed manually or from Jenkins UI)
        ENVIRONMENT     = "${params.ENVIRONMENT ?: 'dev'}"

        // GitOps repository where ArgoCD watches changes (HTTPS)
        GITOPS_REPO     = "https://github.com/Opsmiths-Technologies/opst-deploy-wordpress-app.git"
        GITOPS_BRANCH   = "main"
    }
    parameters {
        string(name: 'ENVIRONMENT', defaultValue: 'dev', description: 'Deployment environment (dev, prod, etc.)')
    }
    stages {
        stage('Initialize') {
            steps {
                script {
                    // Explicitly set APP_NAME to the correct repository name
                    APP_NAME = "wordpress"

                    // Simplified dynamic image tags
                    WORDPRESS_IMAGE_TAG = "${ECR_URL}/${ECR_REPOSITORY}:wordpress-${ENVIRONMENT}-${env.BUILD_NUMBER}"
                    NGINX_IMAGE_TAG     = "${ECR_URL}/${ECR_REPOSITORY}:nginx-${ENVIRONMENT}-${env.BUILD_NUMBER}"
                }
            }
        }
        stage('Build Docker Images') {
            steps {
                sh """
                echo "Building WordPress Image..."
                docker build -t ${WORDPRESS_IMAGE_TAG} -f docker/Dockerfile.dev .
                
                echo "Building Nginx Image..."
                docker build -t ${NGINX_IMAGE_TAG} -f docker/Dockerfile-nginx .
                """
            }
        }
        stage('Login to ECR & Push Images') {
            steps {
                withCredentials([aws(credentialsId: 'aws-credentials', accessKeyVariable: 'AWS_ACCESS_KEY_ID', secretKeyVariable: 'AWS_SECRET_ACCESS_KEY')]) {
                    sh """
                    aws configure set aws_access_key_id ${AWS_ACCESS_KEY_ID}
                    aws configure set aws_secret_access_key ${AWS_SECRET_ACCESS_KEY}
                    aws configure set region ${AWS_REGION}
                    aws ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_URL}

                    echo "Pushing WordPress Image..."
                    docker push ${WORDPRESS_IMAGE_TAG}

                    echo "Pushing Nginx Image..."
                    docker push ${NGINX_IMAGE_TAG}
                    """
                }
            }
        }
        stage('Update GitOps Repository for ArgoCD') {
            steps {
                script {
                    // Extract just the tag portion (e.g., wordpress-dev-123)
                    def wordpressTag = WORDPRESS_IMAGE_TAG.split(':')[-1]
                    def nginxTag = NGINX_IMAGE_TAG.split(':')[-1]

                    // Use username/password credentials for HTTPS
                    withCredentials([usernamePassword(credentialsId: 'github-org-credentials', usernameVariable: 'GITHUB_USERNAME', passwordVariable: 'GITHUB_TOKEN')]) {
                        sh """
                        git clone https://${GITHUB_USERNAME}:${GITHUB_TOKEN}@github.com/Opsmiths-Technologies/opst-deploy-wordpress-app.git
                        cd opst-deploy-wordpress-app

                        # Dynamic path based on the app name and environment
                        APP_KUSTOMIZE_PATH="kubernetes/apps/${APP_NAME}/${ENVIRONMENT}"

                        # Ensure the deployment file exists
                        if [ ! -f "\${APP_KUSTOMIZE_PATH}/kustomization.yaml" ]; then
                            echo "Error: kustomization.yaml not found in \${APP_KUSTOMIZE_PATH}"
                            exit 1
                        fi

                        echo "Updating kustomization.yaml with new image tags..."

                        # Update the WordPress newTag
                        sed -i '/name: wordpress/{n;n;s|newTag:.*|newTag: ${wordpressTag}|}' "\${APP_KUSTOMIZE_PATH}/kustomization.yaml"

                        # Update the Nginx newTag
                        sed -i '/name: nginx/{n;n;s|newTag:.*|newTag: ${nginxTag}|}' "\${APP_KUSTOMIZE_PATH}/kustomization.yaml"

                        # Debug: Print the updated file
                        cat "\${APP_KUSTOMIZE_PATH}/kustomization.yaml"

                        git config user.email "jenkins@opsmiths.com"
                        git config user.name "Jenkins CI"
                        git add "\${APP_KUSTOMIZE_PATH}/kustomization.yaml"
                        git commit -m "Updated ${APP_NAME} (${ENVIRONMENT}) with WordPress=${wordpressTag} & Nginx=${nginxTag}" || echo "Nothing to commit"
                        git push origin ${GITOPS_BRANCH}
                        """
                    }
                }
            }
        }
    }
    post {
        always {
            cleanWs() // Clean workspace after the build
        }
        failure {
            echo "Build failed! Check the logs for details."
        }
        success {
            echo "Build succeeded! Images pushed and GitOps repository updated."
        }
    }
}