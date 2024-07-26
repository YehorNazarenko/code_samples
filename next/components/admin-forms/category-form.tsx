import React from 'react';
import { Button, Form, Input, Popconfirm, Upload } from 'antd';
import { FormProps } from '../../model/form';
import { API_HOST } from '../../main.config';
import { RcFile } from 'antd/es/upload';
import { Media, PageCategory } from '../../model/types.generated';

const { TextArea } = Input;
const CategoryForm = <RecordType extends object = Record<string, unknown>>({
    onFinish,
    initialValues,
    onFinishFailed,
    onConfirmDelete,
    onCancelDelete,
    onCancel,
    isEdit,
}: FormProps<RecordType>): React.ReactElement => {
    const [form] = Form.useForm();
    const image = (initialValues as PageCategory).image;
    const fileInput =
        initialValues && image
            ? [
                  {
                      uid: '-1',
                      name: 'image.png',
                      status: 'done',
                      url: `${API_HOST}/media/images/${image}`,
                  },
              ]
            : [];
    const onUpload = async (file: RcFile) => {
        const formData = new FormData();

        formData.append('name', file.name);
        formData.append('image', file);

        try {
            const res = await fetch(`${API_HOST}/media/upload`, {
                method: 'POST',
                body: formData,
            });

            const data: Media = await res.json();

            form.setFieldValue('image', data.id);

            return data;
        } catch (e) {
            console.log(e);
        }
    };
    const uploadProps = isEdit
        ? {
              fileList: fileInput,
          }
        : {};

    return (
        <Form
            form={form}
            layout="vertical"
            name="basic"
            initialValues={initialValues}
            onFinish={onFinish}
            onFinishFailed={onFinishFailed}
            autoComplete="off"
            requiredMark="optional"
        >
            <Form.Item
                name="name"
                label="Category Name"
                rules={[
                    {
                        required: true,
                        message: 'Please input the Category name',
                    },
                ]}
            >
                <Input placeholder="Tag name" />
            </Form.Item>
            <Form.Item
                name="slug"
                label="Category URL"
                rules={[
                    {
                        required: true,
                        message: 'Please input the uniq Category URL',
                    },
                ]}
            >
                <Input placeholder="tag" />
            </Form.Item>
            <Form.Item
                name="content"
                label="Category Content"
            >
                <TextArea rows={4} />
            </Form.Item>
            <Form.Item name="file" label="Category Image" valuePropName="file">
                {/* @ts-ignore */}
                <Upload
                    // @ts-ignore
                    action={onUpload}
                    listType="picture-card"
                    {...uploadProps}
                >
                    <div>
                        {/* <PlusOutlined /> */}
                        <div style={{ marginTop: 8 }}>Upload</div>
                    </div>
                </Upload>
            </Form.Item>
            <Form.Item name="image" hidden>
                <Input />
            </Form.Item>
            <Form.Item>
                <div className="space">
                    <Button type="primary" htmlType="submit">
                        Save
                    </Button>
                    <Button onClick={onCancel} htmlType="button">
                        Cancel
                    </Button>
                    {isEdit && (
                        <>
                            <div className="space-span" />
                            <Popconfirm
                                title="Delete the Category"
                                description="Are you sure to delete this category?"
                                onConfirm={onConfirmDelete}
                                onCancel={onCancelDelete}
                                okText="Yes"
                                cancelText="No"
                            >
                                <Button type="primary" danger htmlType="button">
                                    Delete
                                </Button>
                            </Popconfirm>
                        </>
                    )}
                </div>
            </Form.Item>
        </Form>
    );
};

export default CategoryForm;
