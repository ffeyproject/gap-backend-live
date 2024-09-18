.PHONY: migrate-init migrate-app migrate-run migrate-fresh

migrate-init:
	echo 'y' | php yii migrate/to m190124_110200_add_verification_token_column_to_user_table				# Initial migration
	echo 'y' | php yii migrate --migrationPath=@yii/rbac/migrations	# RBAC migration

migrate-app:
	echo 'y' | php yii migrate

migrate-run: migrate-init migrate-app

migrate-fresh:
	echo 'y' | php yii migrate/fresh
