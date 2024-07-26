import React from 'react';
import { Button, Form, Input, Select, Space, Popconfirm, Checkbox } from 'antd';
import { FormProps } from '../../model/form';

const MenuForm = <RecordType extends object = Record<string, unknown>>({
    onFinish,
    initialValues,
    onFinishFailed,
    onConfirmDelete,
    onCancelDelete,
    onCancel,
    isEdit,
}: FormProps<RecordType>): React.ReactElement => {
    return (
        <Form
            layout="vertical"
            name="basic"
            initialValues={initialValues}
            onFinish={onFinish}
            onFinishFailed={onFinishFailed}
            autoComplete="off"
            requiredMark="optional"
        >
            <Form.Item name="type" label="Menu type">
                <Select>
                    <Select.Option value="HEADER">Header menu</Select.Option>
                    <Select.Option value="FOOTER">Footer menu</Select.Option>
                </Select>
            </Form.Item>
            <Form.List
                name="links"
                rules={[
                    {
                        validator: async (_, names) => {
                            if (!names || names.length < 1) {
                                return Promise.reject(
                                    new Error('At least 1 Menu link'),
                                );
                            }
                        },
                    },
                ]}
            >
                {(fields, { add, remove }, { errors }) => (
                    <>
                        {fields.map((key, name, ...restField) => (
                            <Space
                                key={name}
                                style={{
                                    display: 'flex',
                                    marginBottom: 8,
                                }}
                                align="baseline"
                            >
                                <Form.Item
                                    {...restField}
                                    name={[name, 'title']}
                                    label="Link Title"
                                    rules={[
                                        {
                                            required: true,
                                            message: 'Missing link title',
                                        },
                                    ]}
                                >
                                    <Input placeholder="Link title" />
                                </Form.Item>
                                <Form.Item
                                    {...restField}
                                    label="Link URL"
                                    name={[name, 'url']}
                                    rules={[
                                        {
                                            required: true,
                                            message: 'Missing link URL',
                                        },
                                    ]}
                                >
                                    <Input placeholder="http://example.com" />
                                </Form.Item>
                                <Form.Item
                                    {...restField}
                                    label="Type of link"
                                    name={[name, 'isExternal']}
                                    valuePropName="checked"
                                >
                                    <Checkbox>Link is external</Checkbox>
                                </Form.Item>
                                <div style={{ padding: '37px 16px 0' }}>
                                    {/* <MinusCircleOutlined
                                        onClick={() => remove(name)}
                                    /> */}
                                </div>
                            </Space>
                        ))}
                        <Form.Item>
                            <Button
                                type="dashed"
                                onClick={() => add()}
                                style={{ width: '100%' }}
                            >
                                Add Menu Link
                            </Button>
                            <Form.ErrorList errors={errors} />
                        </Form.Item>
                    </>
                )}
            </Form.List>
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
                                title="Delete the Menu"
                                description="Are you sure to delete this menu?"
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

export default MenuForm;
