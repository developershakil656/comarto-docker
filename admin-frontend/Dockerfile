# Stage 1: Build
FROM node:20-alpine AS build-stage
WORKDIR /app
COPY app/package*.json ./
RUN npm install
COPY app/ .
RUN npm run build

# Stage 2: Production output
FROM alpine:latest
WORKDIR /var/www/admin-frontend
COPY --from=build-stage /app/dist .
# This command keeps the container from exiting while copying build to shared volume
CMD ["sh", "-c", "cp -a ./. /shared_admin_frontend/ && tail -f /dev/null"]